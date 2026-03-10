import { $t } from '../i18n.js';

class Roles {
    static Listener = 'listener';
    static Creator = 'creator';

    static config = {
        active: { icon: 'file', variant: 'warning' },
        suspended: { icon: 'file-right', variant: 'secondary' },
        cancelled: { icon: 'notification-bing', variant: 'secondary' },
    };

    static options = [
        {
            text: $t('misc.roles.listener.name'),
            value: Roles.Listener,
            description: $t('misc.roles.listener.description'),
        },
        {
            text: $t('misc.roles.creator.name'),
            value: Roles.Creator,
            description: $t('misc.roles.creator.description'),
        },
    ];
}

export default Roles;
