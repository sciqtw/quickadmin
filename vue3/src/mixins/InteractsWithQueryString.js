import defaults from 'lodash/defaults'

export default {
  data() {
    return {
      filterData:{},
    }
  },
  methods: {

    /**
     * Update the given query string values.
     */
    updateFilter(value) {
      this.filterData = defaults(value, this.filterData)
    },
    updateQueryString(value) {
      this.$router.push({ query: defaults(value, this.$route.query) })
    }
  }
}
