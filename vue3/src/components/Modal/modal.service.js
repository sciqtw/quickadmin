import {inject, getCurrentInstance} from "vue";

function getModalContainer(ctx) {
  let parent = ctx?.parent;

  while (
    parent &&
    parent.type &&
    parent.type.name !== 'modal-container'
    ) {
    parent = parent.parent;

  }

  if (parent?.type?.name === "modal-container"){

    return parent;
  }
  else return null;
}


function getParentModalContainer() {
  // const ctx = getCurrentInstance()
  // return getModalContainer(ctx)
  return inject("parentModal")
}

export function useModal() {
  const modal = inject("modal");
  const currentModal = getParentModalContainer();
  return {
    currentModal:currentModal,
    open: (content, options) => {

      return modal.open(content, options)
    },
    close: (type,data) => {

      const modalContainer = currentModal;
      const id = modalContainer.modalId;
      if (!id) return;
      modalContainer.close(type, data);
    },
    closeAll: () => modal.closeAll(),
  };
}
