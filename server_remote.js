var http = require('http');
var server =http.Server();
var io   = require('socket.io'); // 加入 Socket.IO

server.listen(3000);
console.log('listening on *:3000');
var serv_io = io.listen(server);

  //serv_io.set('transports', [ 'websocket' ]);

  serv_io.sockets.on('connection', function(socket) {

  socket.on('get_data',function() {
        //console.log('get_data');
         //socket.emit('date', {'date': new Date()});
         var req =http.request({ host: 'your.domain.name', path: '/data_local.php' }, function(response) {
            var data = "";
            response.on('data', function(chunk) {
                data += chunk;
            });
            response.on('end', function() {    
                serv_io.sockets.emit('data_update', {'data': JSON.parse(data)});
            });
        });
        req.end();
  });
  // setInterval(function() {
  //   socket.emit('date', {'date': new Date()});
  // }, 1000);
});