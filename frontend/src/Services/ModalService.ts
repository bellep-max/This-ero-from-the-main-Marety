import { useModal } from 'vue-final-modal';

export const showModal = async (modal: any, initAttrs: Record<string, any> = {}, props?: any) => {
    const { open, close } = useModal({
        component: modal,
        attrs: {
            data: props,
            onClose() {
                close();
            },
            onConfirm() {
                close();
            },
        },
    });

    return await open();
};

export const hideModal = async (modal: any, initAttrs: Record<string, any> = {}) => {
    const { close } = useModal({
        component: modal,
    });

    return await close();
};

export default { showModal, hideModal };
