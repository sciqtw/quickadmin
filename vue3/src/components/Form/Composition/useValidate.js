import { computed, watch, inject, provide, ref, unref, defineProps } from 'vue';
import AsyncValidator from 'async-validator';
import { Errors } from 'form-backend-validation';

export default function (data, column, rules) {

  const errors = ref(new Errors());

  /**
   * 验证不能为空
   * @type {ComputedRef<boolean>}
   */
  const hasRequired = computed(() => {
    let isRequired = false;

    if (rules && rules.length) {
      rules.every(rule => {
        if (rule.required) {
          isRequired = true;
          return false;
        }
        return true;
      });
    }
    return isRequired;
  });

  /**
   * 验证
   * @returns {boolean}
   */
  const validateData = (event) => {
    const rules = getValiRules(event);
    let valErrors = false;
    if (rules && rules.length > 0) {
      const validator = new AsyncValidator({ [column]: rules });
      validator.validate({ [column]: data.value }, (e) => {
        valErrors = e;
      });
      if (valErrors && valErrors.length) {
        valErrors.forEach(({ message, field }) => {
            errors.value = new Errors({ [field]: message });
          }
        );
      } else {
        errors.value = new Errors({});
      }
    }
    return valErrors;
  };

  /**
   * 获取 field 验证规则
   * @param trigger
   * @returns {({} & T)[]}
   */
  const getValiRules = (trigger) => {

    return rules.filter(rule => {
      if (rule.pattern) {
        // console.log('rule-----rule.pattern->',rule.pattern,rule.pattern.replace(/\"/g,""))
        rule.pattern = eval(rule.pattern);
      }

      if (!rule.trigger || trigger === '') return true;
      if (Array.isArray(rule.trigger)) {
        return rule.trigger.indexOf(trigger) > -1;
      } else {
        return rule.trigger === trigger;
      }
    })
      .map(rule => Object.assign({}, rule,));
  };

  const firstError = computed(() => {
    if (errors.value.has(column)) {
      return errors.value.get(column);
    }
    return '';
  });

  return {
    errors,
    validateData,
    hasRequired,
    firstError,
  };
};
