const API_URL = 'https://net.pupu.li/icinga-public'

Vue.createApp({
    data() {
	return {
	    icinga: { loading: true },
	    doomed: false,
	    info: {
		jonne: "Jyväskylän virtuaalipalvelin",
		mari: "Jyväskylän kerhotilan palvelut",
		morpheus: "Hacklab ry:n Matrix-palvelin",
		katrihelena: "Hacklab ry:n puhelinvaihde",
	    },
	}
    },
    created() {
	// Start fetch
	this.startIcingaFetch()
    },
    methods: {
	startIcingaFetch() {
	    const updateVisitors = async () => {
		this.icinga = await (await fetch(API_URL)).json()
	    }
	    // Set up recurring timer
	    setInterval(updateVisitors, 60000)
	    // Do initial data fetch
	    updateVisitors()
	},
    }
}).mount('#app')


