$(document).ready(function () {
   App.init();
});

let App = new function () {
    let t = this;

    t.init = function () {
        t.clickLoadMore();
    };

    t.clickLoadMore = function () {
        $('.load_more').click(function () {
            axios.get('/api/last')
                .then(function (response) {
                    let html = '';
                    let data = response.data;
                    let template = $('#img_one').html();
                    let i;
                    for (i in data) {
                        if(data.hasOwnProperty(i)) {
                            let item = data[i];

                        }
                    }
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .then(function () {
                    // always executed
                });
        });
    }
};