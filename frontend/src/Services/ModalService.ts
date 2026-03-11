export const showModal = async (_modal: any, _initAttrs: Record<string, any> = {}, _props?: any) => {
    // In React, modals are controlled via state (show/onClose props).
    // This service is kept for backward compatibility but is a no-op.
    console.warn('ModalService.showModal is a no-op in React. Use component state to control modals.');
};

export const hideModal = async (_modal: any, _initAttrs: Record<string, any> = {}) => {
    console.warn('ModalService.hideModal is a no-op in React. Use component state to control modals.');
};

export default { showModal, hideModal };
