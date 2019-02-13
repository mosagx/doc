var db = require('mysql');

var conn = db.createConnection({
    host : '127.0.0.1',
    user : 'root',
    password : 'root',
    database : 'op_server'
});

conn.connect();

conn.query('SELECT * FROM manager', function (error, result) {
    if (error) {
        console.log('[SELECT ERROR] - ',error.message);
        return;
    }
    console.log(result);
});