import _ from 'lodash'
// import { escapeUnicode } from '@/util/escapeUnicode'

export default {
  namespaced: true,

  state: () => ({
    filters: [],
    originalFilters: [],
  }),

  getters: {
    /**
     * The filters for the resource
     */
    filters: state => state.filters,

    /**
     * The original filters for the resource
     */
    originalFilters: state => state.originalFilters,

    /**
     * Determine if there are any filters for the resource.
     */
    hasFilters: state => Boolean(state.filters.length > 0),

    /**
     * The current unencoded filter value payload
     */
    currentFilters: (state, getters) => {
      return _.map(state.filters, f => {
        return {
          class: f.class,
          value: f.currentValue,
        }
      })
    },

    getFilter: state => filterKey => {
      return _.find(state.filters, filter => {
        return filter.class == filterKey
      })
    },

    getOriginalFilter: state => filterKey => {
      return _.find(state.originalFilters, filter => {
        return filter.class == filterKey
      })
    },
    /**
     * Get the options for a single filter.
     */
    getOptionsForFilter: (state, getters) => filterKey => {
      const filter = getters.getFilter(filterKey)
      return filter ? filter.options : []
    },

    /**
     * Get the current value for a given filter at the provided key.
     */
    filterOptionValue: (state, getters) => (filterKey, optionKey) => {
      const filter = getters.getFilter(filterKey)

      return _.find(filter.currentValue, (value, key) => key == optionKey)
    },
  },

  actions: {
    setFilter({ commit, state }, data){
      commit('storeFilters', data)
    }
  },

  mutations: {

    /**
     *
     * @param state
     * @param filterClass
     * @param value
     */
    updateFilterState(state, { filterClass, value }) {
      const filter = _(state.filters).find(f => f.class === filterClass)
      filter.currentValue = value
    },

    /**
     *
     * @param state
     * @param data
     */
    storeFilters(state, data) {
      state.filters = data
      state.originalFilters = _.cloneDeep(data)
    },

    /**
     *
     * @param state
     */
    clearFilters(state) {
      state.filters = []
      state.originalFilters = []
    },
  },
}
