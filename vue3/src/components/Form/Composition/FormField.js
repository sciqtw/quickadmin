export const baseProps = {
  default: {
    default: ''
  },
  column: {
    type: String,
    default: 'name'
  },
  title: {
    type: String,
    default: ''
  },
  validate: {
    type: Array,
    default: () => []
  },
  disabled: {
    type: Boolean,
    default: false
  },
};

export function contrasts(condition, option, fieldValue) {
// =、>、>=、<、<=、!=、in、notIn

  var n = Number(fieldValue)
  if (!isNaN(n) && !Array.isArray(fieldValue)) {
    fieldValue = n
  }
  switch (condition) {
    case '=':
      if (Array.isArray(fieldValue) && Array.isArray(option)) {
        if (fieldValue.length === option.length && _.difference(option, fieldValue).length === 0) {
          return true
        }
      } else {
        if (fieldValue == option) {
          return true
        }
      }

      break
    case '>':
      if (fieldValue > option) {
        return true
      }
      break
    case '>=':
      if (fieldValue >= option) {
        return true
      }
      break
    case '<':
      if (fieldValue < option) {
        return true
      }
      break
    case '<=':
      if (fieldValue <= option) {
        return true
      }
      break
    case '!=':

      if (Array.isArray(fieldValue) && Array.isArray(option)) {
        if (fieldValue.length !== option.length || _.difference(option, fieldValue).length > 0) {
          return true
        }
      } else {
        if (fieldValue !== option) {
          return true
        }
      }

      break
    case 'in':

      if (Array.isArray(fieldValue)) {
        if (Array.isArray(option) && _.difference(option, fieldValue).length == 0) {
          return true
        }
      } else {
        if (Array.isArray(option) && option.includes(fieldValue)) {
          return true
        }
      }

      break
    case 'has':
      if (Array.isArray(fieldValue) && fieldValue.includes(option)) {
        return true
      }

      break
    case 'notIn':
      if (Array.isArray(option) && !option.includes(fieldValue)) {
        return true
      }
      break
  }
  return false
}
