import { computed, getCurrentInstance, inject, provide, ref, unref,defineProps } from 'vue';

export const userAddField = () => {

  const groupForm = inject('groupForm', {});
  const form = inject('form');
  const bus = inject('formBus');

  return {
    groupForm,
    form,
    bus,
  };
};
