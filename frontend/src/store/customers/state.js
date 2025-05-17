const state = () => ({
    customers: {
        loading: true,
        data: [],
        links: [],
        from: null,
        to: null,
        page: 1,
        limit: null,
        total: null,
    },
});

export default state;