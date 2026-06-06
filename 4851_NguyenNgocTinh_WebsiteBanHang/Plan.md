tôi ở trang http://localhost:88/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/Product?sort_by=newest&page=1

và ấn nút xoá bên dưới các sản phẩm thì báo lỗi 
[SocketManager] Initiating connection for token: TokenPresent
requests.js:1  DELETE http://localhost:88/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/api/product/3 401 (Unauthorized)
s.XMLHttpRequest.send @ requests.js:1
send @ jquery-3.7.1.min.js:2
ajax @ jquery-3.7.1.min.js:2
(anonymous) @ Product?sort_by=newest&page=1:1282
Promise.then
then @ sweetalert2@11:5
confirmDelete @ Product?sort_by=newest&page=1:1280
onclick @ Product?sort_by=newest&page=1:1Understand this error
Product?sort_by=newest&page=1:42 WebSocket connection to 'ws://localhost:8080/ws?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3ODA3MTY1MDMsImV4cCI6MTc4MDcyMDEwMywiZGF0YSI6eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsInJvbGUiOiJhZG1pbiJ9fQ.SExFAWr2j0rGaWDqRtAqVkAV1_BFd1ql_QUtrFgmY50' failed: 
connect @ Product?sort_by=newest&page=1:42
(anonymous) @ Product?sort_by=newest&page=1:776Understand this error
Product?sort_by=newest&page=1:54 [SocketManager] WebSocket error: Event {isTrusted: true, type: 'error', target: WebSocket, currentTarget: WebSocket, eventPhase: 2, …}
socket.onerror @ Product?sort_by=newest&page=1:54Understand this error
Product?sort_by=newest&page=1:58 [SocketManager] WebSocket connection closed: 
requests.js:1  DELETE http://localhost:88/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/api/product/3 401 (Unauthorized)