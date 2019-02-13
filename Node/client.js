var http = require('http');

var options = {
    host  : 'localhost',
    port  : '8080',
    path  : '/quote/random',
    method: 'GET'
}

console.info('Options prepared:');
console.info(options);
console.info('Do the GET call');

var reqGet = http.request(options, function (res) {
    console.log('Status Code:', res.statusCode);

    console.log("Headers: ", res.headers);

    res.on('data', function (data) {
        console.info('GET result:\n');
        process.stdout.write(data);
        console.info('\n\nCall Completed')
    });
});

reqGet.end();
reqGet.on('error', function(e) {
    console.error(e);
});