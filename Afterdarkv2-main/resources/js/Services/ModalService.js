import { useModal } from 'vue-final-modal';

export const showModal = async (modal, initAttrs = {}, props) => {
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

export const hideModal = async (modal, initAttrs = {}) => {
    const { close } = useModal({
        component: modal,
    });

    return await close();
};

export default { showModal, hideModal };
