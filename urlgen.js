let url = 'http://localhost/phpstudy/imgmake/get_img_api.php'
let imgData = {
  images: [
    {
      x: -0,                  // x坐标，负数表示从右计算
      y: -0.001,                  // y坐标，负数表示从下计算
      w: 300,                  // 缩放后的宽度，w，h两个都有两个都算，只有一个等比缩放
      h: 500,
      path: 'https://unsplash.it/500/1000/?random',               // 图片路径
      stretch: true
    }
  ],
  // texts: [
  //   {
  //     x: 0,
  //     y: 0,
  //     w: 300,
  //     color: '#999999',
  //     // font:"",
  //     // fontsize:"",
  //     // alpha:0,
  //     // enRate:"",
  //     stretch: true,
  //     text: '刘壮壮是煞笔'
  //   }
  // ],
  canvas: {
    w: 500,
    h: 1000,
    stretch: false
  }
}
url = url + '?imgData=' + encodeURIComponent(JSON.stringify(imgData))
console.log(url)
