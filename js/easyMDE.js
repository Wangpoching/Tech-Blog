// ① EasyMDE
const easyMDE = new EasyMDE({
  element: document.getElementById('content'),
  uploadImage: true,
  imageUploadEndpoint: '../upload_image.php',
  imagePathAbsolute: true,
  imageMaxSize: 2 * 1024 * 1024,
  imageAccept: 'image/jpeg, image/png, image/webp, image/gif' 
});