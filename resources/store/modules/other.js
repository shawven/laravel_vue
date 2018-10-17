import http from '@/libs/http';

function getUserChannels() {
    let channels = [];
    http.get('/api/users/channels').then((result) => {
        channels.push(...result.data.data.list)
    }).catch((error) => http.handler.handleError(error));

    return channels
}

const other = {
    state: {
        userChannels: []
    },
    getters: {
        userChannels(state) {
            if (state.userChannels.length === 0) {
                state.userChannels = getUserChannels();
            }

            return state.userChannels;
        },

    }
};

export default other;
