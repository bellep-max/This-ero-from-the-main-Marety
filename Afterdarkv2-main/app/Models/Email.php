<?php

namespace App\Models;

use App\Jobs\SendEmail;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public $fillable = [
        'type',
        'description',
        'subject',
        'content',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'string',
        'subject' => 'string',
        'content' => 'string',
    ];

    /**
     * Validation rules.
     */
    public static array $rules = [
        'type' => 'required',
        'subject' => 'required',
    ];

    public function parse($data)
    {
        return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($data) {
            [$shortCode, $index] = $matches;
            if (isset($data[$index])) {
                return $data[$index];
            } else {
                /*
                 * for testing only
                 */
                // throw new Exception("Shortcode {$shortCode} not found in template id {$this->id}", 1);
            }
        }, $this->content);
    }

    public function newUser($user)
    {
        $data = ['name' => $user->name];
        dispatch(new SendEmail('newUser', $user->email, $data));
    }

    public function verifyAccount($user, $validationLink): void
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'validationLink' => $validationLink,
        ];

        dispatch(new SendEmail('verifyAccount', $user->email, $data));
    }

    public function resetPassword($user, $resetLink)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'resetLink' => $resetLink,
        ];
        dispatch(new SendEmail('resetPassword', $user->email, $data));
    }

    public function newArtist($user, $artist)
    {
        $data = [
            'artistName' => $artist->name,
        ];

        dispatch(new SendEmail('newArtist', $user->email, $data));
    }

    public function feedback($feedback)
    {
        $data = [
            'email' => $feedback->email,
            'feeling' => $feedback->feeling,
            'about' => $feedback->about,
            'comment' => $feedback->comment,
            'ip' => request()->ip(),
        ];

        dispatch(new SendEmail('feedback', config('settings.admin_mail'), $data, $feedback->email));
    }

    public function subscribePlaylist($user, $playlist)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'playlistName' => $playlist->title,
            'playlistLink' => $playlist->permalink,
        ];

        dispatch(new SendEmail('shareMedia', $user->email, $data));
    }

    public function followUser($user, $follower)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'followerName' => $follower->name,
            'followerLink' => $follower->url,
        ];

        dispatch(new SendEmail('followUser', $user->email, $data));
    }

    public function newComment($comment)
    {
        if ($comment->commentable_type == Playlist::class) {
            $object = Playlist::findOrFail($comment->commentable_id);
        } elseif ($comment->commentable_type == Song::class) {
            $object = Song::findOrFail($comment->commentable_id);
        } elseif ($comment->commentable_type == Album::class) {
            $object = Album::findOrFail($comment->commentable_id);
        } elseif ($comment->commentable_type == Artist::class) {
            $object = Artist::findOrFail($comment->commentable_id);
        } elseif ($comment->commentable_type == User::class) {
            $object = User::findOrFail($comment->commentable_id);
        } elseif ($comment->commentable_type == Post::class) {
            $object = Post::findOrFail($comment->commentable_id);
        } elseif ($comment->commentable_type == Station::class) {
            $object = Station::findOrFail($comment->commentable_id);
        }

        $data = [
            'name' => $comment->user->name,
            'username' => $comment->user->username,
            'created_at' => $comment->created_at,
            'url' => $object->permalink,
            'text' => $comment->content,
        ];

        dispatch(new SendEmail('newComment', config('settings.admin_mail'), $data));
    }

    public function rejectedSong($user, $song, $comment)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'title' => $song->title,
            'url' => $song->permalink,
            'comment' => $comment,
        ];

        dispatch(new SendEmail('rejectedSong', $user->email, $data));
    }

    public function rejectedAlbum($user, $album, $comment)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'title' => $album->title,
            'url' => $album->permalink,
            'comment' => $comment,
        ];

        dispatch(new SendEmail('rejectedAlbum', $user->email, $data));
    }

    public function approvedSong($user, $song)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'title' => $song->title,
            'url' => $song->permalink,
        ];

        dispatch(new SendEmail('approvedSong', $user->email, $data));
    }

    public function approvedAlbum($user, $album)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'title' => $album->title,
            'url' => $album->permalink,
        ];

        dispatch(new SendEmail('approvedAlbum', $user->email, $data));
    }

    public function rejectedArtist($request, $comment = '')
    {
        $data = [
            'name' => $request->user->name,
            'artist_name' => $request->artist_name,
            'comment' => $comment,
        ];

        dispatch(new SendEmail('rejectedArtist', $request->user->email, $data));
    }

    public function approvedArtist($request)
    {
        $data = [
            'name' => $request->user->name,
            'artist_name' => $request->artist_name,
        ];

        dispatch(new SendEmail('approvedArtist', $request->user->email, $data));
    }
}
