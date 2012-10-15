$(function() {
  var drag = document.getElementById('dragarea')
    , edit = document.getElementById('editarea')
    , img = document.getElementById('edit-img')
    , form = new FormData()
    , jcrop_api
    , cx, cy, cw, ch

  function uploadImg(e, f) {
    var $btn = $('.img-select-btn')
    if (!f.type.match(/image.*/)) {
      msg(2, '这不是一张图片')
      return false
    } else if (f.size >= 4 * 1024 * 1024) {
      msg(2, '请上传不超过4M的图片')
    }
    form.append('image', f)
    form.append({type:'uploadPhoto', uid:uid, auth:auth})
    $btn.button('loading')
    $.ajax({
        processData: false
      , contentType: false
      , data: form
      , success: function (r) {
          $btn.button('reset')
          if (r[0] == 0) {
            msg(2, r[1])
          } else if (r[0] == 1) {
            img.setAttribute('src', r[1])
            img.setAttribute('style', '')
            $('#edit-img').Jcrop({
              setSelect: [5, 5, 275, 275],
              minSize: [96, 96],
              aspectRatio: 1,
              onChange: function (c) {
                cx = c.x
                cy = c.y
                cw = c.w
                ch = c.h
              },
              onSelect: function (c) {
                cx = c.x
                cy = c.y
                cw = c.w
                ch = c.h
              }
            }, function () {
              jcrop_api = this
            })
            edit.style.display = 'block'
          }
        }
    })
  }

  $.ajaxSetup({url:'/php/account_ajax.php', type:'post', dataType:'json', timeout:7000})

  $('body').ajaxSend(function (e, x, s) {
    switch (s.data.split('&')[0]) {
      case 'type=basic':
        msg(3, '正在保存...')
        break
      case 'type=security':
        msg(3, '正在保存...')
        break
      case 'type=uploadPhoto':
        msg(3, '请稍候...')
        break
      case 'type=savePhoto':
        msg(3, '正在保存...')
        break
    }
  })

  $("form[name='basic']").submit(function (e) {
    var $name = $("input[name='name']").blur()
      , $domain = $("input[name='domain']").blur()
      , $mail = $("input[name='mail']").blur()
      , name = $name.val()
      , domain = $domain.val()
      , mail = $mail.val()
    e.preventDefault()
    if (!bName || !bDo || !bMail) return false
    $.ajax({
        data: {type:'basic', uid:uid, auth:auth, name:name, domain:domain, mail:mail}
      , success: function (r) {
          if (r[0] == 0) return msg(4, r[1])
          msg(2, '已保存')
          sName = name
          sDo = domain
          sMail = mail
          $name.showHelp(0)
          $domain.showHelp(0)
          $mail.showHelp(0)
        }
    })
  })
  $("form[name='security']").submit(function (e) {
    var $old = $("input[name='oldpwd']")
      , $password = $("input[name='password']").blur()
      , $repassword = $("input[name='repassword']").blur()
      , old = $old.val()
      , password = $password.val()
    e.preventDefault()
    if (!bPass && !bRepass) return false
    $.ajax({
        data: {type:'security', uid:uid, auth:auth, old:old, password:password}
      , success: function (r) {
          if (r[0] == 0) return msg(3, r[1])
          msg(2, '您的密码已更新')
          $password.showHelp(0)
          $repassword.showHelp(0)
        }
    })
  })

  window.addEventListener('dragover', function (e) {
    e.stopPropagation()
    e.preventDefault()
  }, false)
  window.addEventListener('drop', function (e) {
    e.stopPropagation()
    e.preventDefault()
  }, false)
  drag.addEventListener('dragenter', function (e) {
    this.setAttribute('class', 'dragarea hover')
  }, false)
  drag.addEventListener('dragleave', function (e) {
    this.setAttribute('class', 'dragarea')
  }, false)
  drag.addEventListener('dragover', function (e) {
    e.stopPropagation()
    e.preventDefault()
    this.setAttribute('class', 'dragarea hover')
  }, false)

  drag.addEventListener('drop', function (e) {
    var files = e.dataTransfer.files
      , f = files[0]
    e.stopPropagation()
    e.preventDefault()
    drag.setAttribute('class', 'dragarea')
    uploadImg(e, f)
  }, false)
  // document.getElementById('invisible-input').addEventListener('change', function (e) {
  //   var files = e.target.files
  //     , f = files[0]
  //   uploadImg(e, f)
  // }, false)
  $('#invisible-input').change(function (e) {
    var files = e.target.files
      , f = files[0]
    uploadImg(e, f)
  })

  $('#save-photo').click(function () {
    if (!cx || !cy || !cw || !ch) return false
    $.ajax({
        data: {type:'savePhoto', uid:uid, auth:auth, x:cx, y:cy, w:cw, h:ch}
      , success: function (r) {
          if (r[0] == 0) return msg(2, r[1])
          $('.userpanel-avatar, .usr-avatar').attr('src', r[1])
          $('#sbtn_ava').next().click()
          msg(2, '您的照片已更新')
        }
    })
  })
  $('#cancle-photo').click(function(){
    if (jcrop_api) {
      jcrop_api.destroy()
    }
    cx = 0
    cy = 0
    cw = 0
    ch = 0
    $('#invisible-input').val('')
    edit.style.display = 'none'
  })
})