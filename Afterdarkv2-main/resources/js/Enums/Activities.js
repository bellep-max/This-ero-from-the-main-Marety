import { $t } from '../i18n.js';

class Activities {
    static AddSong = 'add_song';
    static AddEpisode = 'add_episode';
    static AddEvent = 'add_event';
    static AddMood = 'add_mood';
    static AddGenre = 'add_genre';
    static AddCategory = 'add_category';
    static StoreCategory = 'save_add_category';
    static StoreGenre = 'save_add_genre';
    static StoreMood = 'save_add_mood';
    static EditCategory = 'change_category';
    static EditAuthor = 'change_author';
    static EditGenre = 'change_genre';
    static EditMood = 'change_mood';
    static EditArtist = 'change_artist';
    static EditAlbum = 'change_album';
    static UpdateAuthor = 'save_change_author';
    static UpdateCategory = 'save_change_category';
    static UpdateGenre = 'save_change_genre';
    static UpdateMood = 'save_change_mood';
    static UpdateArtist = 'save_change_artist';
    static UpdateAlbum = 'save_change_album';
    static CollectSong = 'collect_song';
    static FavoriteSong = 'favorite_song';
    static FavoriteAlbum = 'favorite_album';
    static FavoriteEpisode = 'favorite_episode';
    static AddToPlaylist = 'add_to_playlist';
    static FollowPodcast = 'follow_podcast';
    static RemovePodcastEpisode = 'remove_from_podcast';
    static RemovePlaylistSong = 'remove_from_playlist';
    static Love = 'love';
    static Unlove = 'unlove';
    static InviteCollaboration = 'invite_collaborate';
    static AcceptedCollaboration = 'accepted_collaboration';
    static Invite = 'invite';
    static Accept = 'accept';
    static Cancel = 'cancel';
    static SharedMusic = 'shared_music';
    static CommentMentioned = 'comment_mentioned';
    static CommentMusic = 'comment_music';
    static CommentReply = 'reply_comment';
    static CommentReact = 'react_comment';
    static Verified = 'verified';
    static Unverified = 'unverified';
    static Approve = 'approve';
    static NotApprove = 'not_approve';
    static Fixed = 'fixed';
    static NotFixed = 'not_fixed';
    static Commented = 'comments';
    static Uncommented = 'not_comments';
    static ClearCount = 'clear_count';
    static ClearViews = 'clear_views';
    static ClearTags = 'clear_tags';
    static SetCurrent = 'set_current';
    static Show = 'visibility';
    static Hide = 'private';
    static Delete = 'delete';
    static PlaySong = 'play_song';

    static config = {
        add_song: { icon: 'file-audio' },
        add_episode: { icon: 'folder-plus' },
        add_event: { icon: 'folder-plus' },
        add_mood: { icon: 'folder-plus' },
        add_genre: { icon: 'folder-plus' },
        add_category: { icon: 'folder-plus' },
        save_add_category: { icon: 'folder-plus' },
        save_add_genre: { icon: 'folder-plus' },
        save_add_mood: { icon: 'folder-plus' },
        change_category: { icon: 'right-left' },
        change_author: { icon: 'right-left' },
        change_genre: { icon: 'right-left' },
        change_mood: { icon: 'right-left' },
        change_artist: { icon: 'right-left' },
        change_album: { icon: 'right-left' },
        save_change_author: { icon: 'arrow-turn-to-dots' },
        save_change_category: { icon: 'arrow-turn-to-dots' },
        save_change_genre: { icon: 'arrow-turn-to-dots' },
        save_change_mood: { icon: 'arrow-turn-to-dots' },
        save_change_artist: { icon: 'arrow-turn-to-dots' },
        save_change_album: { icon: 'arrow-turn-to-dots' },
        collect_song: { icon: 'folder-plus' },
        favorite_song: { icon: 'heart-circle-plus' },
        favorite_album: { icon: 'heart-circle-plus' },
        favorite_episode: { icon: 'heart-circle-plus' },
        add_to_playlist: { icon: 'folder-plus' },
        follow_podcast: { icon: 'circle-plus' },
        remove_from_podcast: { icon: 'folder-minus' },
        remove_from_playlist: { icon: 'folder-minus' },
        love: { icon: 'heart-circle-plus' },
        unlove: { icon: 'heart-circle-minus' },
        invite_collaboration: { icon: 'user-plus' },
        accepted_collaboration: { icon: 'user-check' },
        invite: { icon: 'user-plus' },
        accept: { icon: 'user-check' },
        cancel: { icon: 'user-xmark' },
        shared_music: { icon: 'share' },
        comment_mentioned: { icon: 'comment' },
        comment: { icon: 'comment' },
        reply_comment: { icon: 'comment' },
        react_comment: { icon: 'comment' },
        verified: { icon: 'check' },
        unverified: { icon: 'xmark' },
        approve: { icon: 'check' },
        not_approve: { icon: 'xmark' },
        fixed: { icon: 'wrench' },
        not_fixed: { icon: 'wrench' },
        comments: { icon: 'comment' },
        not_comments: { icon: 'comment' },
        clear_count: { icon: 'eraser' },
        clear_views: { icon: 'eraser' },
        clear_tags: { icon: 'eraser' },
        set_current: { icon: 'thumbtack' },
        visibility: { icon: 'eye-slash' },
        private: { icon: 'lock' },
        delete: { icon: 'trash' },
    };

    static getIcon = (activity) => this.config[activity.action].icon;

    static getText = (activity) =>
        $t(`events.${activity.action}`, {
            user: activity.user?.username,
            title: activity.subject.title ?? activity.subject.uuid,
        });

    static getLink = (activity) => {
        if (!activity || activity.subject?.type === 'Activities') {
            return '';
        }

        if (!activity.subject?.uuid) {
            return '';
        }

        try {
            return route(`${activity.subject.type.toLowerCase()}.show`, activity.subject.uuid);
        } catch (e) {
            console.warn('Failed to generate route for notification:', activity, e);
            return '';
        }
    };
}

export default Activities;
