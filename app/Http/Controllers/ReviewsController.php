<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Review;
use App\Models\User;
use App\Notifications\Admin\PendingReviewNotification;
use App\Notifications\ReviewApprovedNotification;
use App\Notifications\ReviewReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ReviewsController extends Controller
{
    // ── Admin ────────────────────────────────────────────────────────────────

    public function index()
    {
        $reviews = Review::with(['anime', 'user'])
            ->withCount('replies')
            ->whereNull('parent_id')
            ->latest()
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    // ── Public ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'body'     => 'required|min:10|max:2000',
            'rate'     => 'required|integer|min:1|max:10',
            'anime_id' => 'required|exists:animes,id',
        ]);

        $userId = auth()->id();

        if (Review::where('anime_id', $validated['anime_id'])
            ->where('user_id', $userId)
            ->whereNull('parent_id')
            ->exists()) {
            return back()->withErrors(['anime_id' => 'You have already reviewed this anime.']);
        }

        $validated['user_id'] = $userId;
        $validated['is_verified_watcher'] = auth()->user()
            ->watchHistory()->where('anime_id', $validated['anime_id'])->exists()
            || auth()->user()->watchList()
                ->wherePivot('status', 'watching')
                ->where('anime_id', $validated['anime_id'])
                ->exists();

        $review = Review::create($validated);
        $review->load(['anime', 'user']);

        $admins = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->get();
        Notification::send($admins, new PendingReviewNotification($review));

        return back()->with('success', __('Review added successfully.'));
    }

    public function update(Request $request, $id)
    {
        // Admin approve / reject
        if ($request->has('is_active')) {
            abort_unless(auth()->user()?->isAdmin(), 403);

            $validated = $request->validate(['is_active' => 'required|boolean']);
            $review    = Review::findOrFail($id);
            $wasInactive = ! $review->is_active;
            $review->update($validated);

            AuditLog::record('review.' . ($review->is_active ? 'approved' : 'rejected'), $review,
                ['is_active' => ! $review->is_active], ['is_active' => $review->is_active]);

            if ($wasInactive && $review->is_active && $review->user) {
                try {
                    $review->user->notify(new ReviewApprovedNotification($review));
                } catch (\Exception $e) {}
            }

            $msg = $review->is_active ? 'Review approved.' : 'Review disapproved.';
            return redirect('/admin/reviews')->with('success', $msg);
        }

        // User editing own review
        $review    = Review::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $validated = $request->validate([
            'body' => 'required|min:10|max:2000',
            'rate' => 'required|integer|min:1|max:10',
        ]);
        $review->update($validated);

        return back()->with('success', __('Review updated.'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if (auth()->user()->isAdmin() || $review->user_id === auth()->id()) {
            AuditLog::record('review.deleted', $review, ['body' => $review->body], []);
            $review->delete();
        } else {
            abort(403);
        }

        if (request()->routeIs('reviews.*')) {
            return redirect('/admin/reviews')->with('deleted_review', 'Review deleted.');
        }

        return back()->with('success', __('Review deleted.'));
    }

    public function storeReply(Request $request)
    {
        $validated = $request->validate([
            'body'      => 'required|min:5|max:1000',
            'review_id' => 'required|exists:reviews,id',
        ]);

        $parent = Review::findOrFail($validated['review_id']);

        $reply = Review::create([
            'anime_id'  => $parent->anime_id,
            'user_id'   => auth()->id(),
            'parent_id' => $parent->id,
            'body'      => $validated['body'],
            'rate'      => null,
            'is_active' => true,
        ]);

        if ($parent->user_id !== auth()->id() && $parent->user) {
            try {
                $parent->user->notify(new ReviewReplyNotification($parent, $reply));
            } catch (\Exception $e) {}
        }

        return back()->with('success', __('Reply posted.'));
    }

    public function voteReview(Request $request, $idReview)
    {
        $validated = $request->validate(['vote' => 'required|boolean']);

        $isUpVote = $validated['vote'];
        $userId   = auth()->id();
        $review   = Review::findOrFail($idReview);

        if ($review->user_id === $userId) {
            return back()->withErrors(['vote' => 'You cannot vote on your own review.']);
        }

        $vote = $review->users()->where('user_id', $userId)->first();

        if ($vote) {
            if ($isUpVote == $vote->pivot->is_upvote) {
                $review->users()->detach($userId);
            } else {
                $review->users()->updateExistingPivot($userId, ['is_upvote' => $isUpVote]);
            }
        } else {
            $review->users()->attach($userId, ['is_upvote' => $isUpVote]);
        }

        return back()->with('success', __('Vote recorded.'));
    }
}
