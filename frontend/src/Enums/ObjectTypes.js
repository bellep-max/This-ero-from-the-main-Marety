class ObjectTypes {
    static Song = 'song';
    static Album = 'album';
    static Artist = 'artist';
    static Station = 'station';
    static Playlist = 'playlist';
    static Podcast = 'podcast';
    static PodcastEpisode = 'episode';
    static User = 'user';
    static Adventure = 'adventure';

    static getObjectType = (item) => {
        return item.toLowerCase().split('\\').pop();
    };

    static icons = {
        [ObjectTypes.Song]: 'play',
        [ObjectTypes.Adventure]: 'code-branch',
        [ObjectTypes.Podcast]: 'podcast',
    };
}

export default ObjectTypes;
