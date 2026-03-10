class SubscriptionStatus {
    static Active = 'active';
    static Suspended = 'suspended';
    static Cancelled = 'cancelled';

    static config = {
        active: { icon: 'file', variant: 'warning' },
        suspended: { icon: 'file-right', variant: 'secondary' },
        cancelled: { icon: 'notification-bing', variant: 'secondary' },
    };
}

export default SubscriptionStatus;
