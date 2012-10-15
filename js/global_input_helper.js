var bName, sName, bDo, sDo, bMail, sMail, bPass, bRepass

$(function () {
  $.fn.showHelp = function (mode, string) {
    return this.each(function () {
      var $that = $(this).next()
      if (mode == 1) {
        $that.addClass('warning').removeClass('error success').html(string)
      } else if (mode == 2) {
        $that.addClass('success').removeClass('warning error').html(string)
      } else if (mode == 4) {
        $that.addClass('error').removeClass('warning success').html(string)
      } else if (mode == 0) {
        $that.empty()
      }
    })
  }

  $.ajaxSetup({url:'/php/account_ajax.php', type:'post', dataType:'json', timeout:7000})

  $('body').ajaxError(function () {
    msg(4, '发生错误，请稍候重试...')
  })

  $("input[name='name']").bind({
      focus: function () {
        $(this).showHelp(1, '4-32个英文字符，2-16个中文字符，可以有下划线和减号')
      }
    , blur: function () {
        var $this = $(this)
          , s = $this.val()
          , reg = /^[-_a-zA-Z0-9\u4e00-\u9fa5]+$/
          , len = s.replace(/[^\x00-\xff]/g, 'aa').length
        if (!s) {
          bName = 0
          $this.showHelp(0)
        } else if (s == sName) {
          bName = 1
          $this.showHelp(0)
        } else if (len < 4 || !reg.test(s) || len > 32) {
          bName = 0
          $this.showHelp(4, '格式没有符合要求...')
        } else {
          $this.showHelp(1, '正在检查...')
          $.ajax({
              data: {type:'checkName', name:s}
            , success: function (r) {
                r == 1 && $this.showHelp(4, '已经被使用...') && (bName = 0)
                r == 0 && $this.showHelp(2, '好的!') && (bName = 1)
              }
          })    
        }
      }
  })

  $("input[name='domain']").bind({
      focus: function () {
        $(this).showHelp(1, '4-32个英文字符，可以有下划线和减号')
      }
    , blur: function () {
        var $this = $(this)
          , s = $this.val()
          , reg = /^[-_a-zA-Z0-9]{4,32}$/
        if (!s) {
          bDo = 0
          $this.showHelp(0)
        } else if (s == sDo) {
          bDo = 1
          $this.showHelp(0)
        } else if (!reg.test(s)) {
          bDo = 0
          $this.showHelp(4, '格式没有符合要求...')
        } else {
          $this.showHelp(1, '正在检查...')
          $.ajax({
              data: {type:'checkDomain', domain: s}
            , success: function (r) {
                r == 1 && $this.showHelp(4, '已经被使用...') && (bDo = 0)
                r == 0 && $this.showHelp(2, '好的!') && (bDo = 1)
              }
          })
        }
      }
  })

  $("input[name='mail']").bind({
      focus: function () {
        $(this).showHelp(1, '请输入真实邮箱，我们将发送验证邮件给您')
      }
    , blur: function () {
        var $this = $(this)
          , s = $this.val()
          , reg = /^[_\.0-9a-zA-Z+-]+@([0-9a-zA-Z]+[0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$/
        if (!s) {
          bMail = 0
          $this.showHelp(0)
        } else if (s == sMail) {
          bMail = 1
          $this.showHelp(0)
        } else if (!reg.test(s)) {
          bMail = 0
          $this.showHelp(4, '请输入有效的邮箱地址...')
        } else {
          $this.showHelp(1, '正在检查...')
          $.ajax({
              data: {type:'checkMail', mail: s}
            , success: function (r) {
                r == 1 && $this.showHelp(4, '该邮箱已经被注册') && (bMail = 0)
                r == 0 && $this.showHelp(2, '好的!') && (bMail = 1)
              }
          })
        }
      }
  })

  $("input[name='password']").bind({
      focus: function () {
        $(this).showHelp(1, '6-32位英文字母，数字，符号，区分大小写')
      }
    , blur: function () {
        var $this = $(this)
          , s = $this.val()
          , reg = /^[\x00-\xff]{6,32}$/
        if (!s) {
          bPass = 0
          $this.showHelp(0)
        } else if (s.length < 6) {
          bPass = 0
          $this.showHelp(4, '密码太短')
        } else if (!reg.test(s)) {
          bPass = 0
          $this.showHelp(4, '密码格式不正确')
        } else {
          bPass = 1
          $this.showHelp(2, '好的!')
        }
      }
  })

  $("input[name='repassword']").bind({
      focus: function () {
        $(this).showHelp(1, '请再输入一遍密码以防止失误')
      }
    , blur: function () {
        var $this = $(this)
          , s = $this.val()
          , s0 = $("input[name='password']").val()
        if (!s && !s0) {
          bRepass = 0
          $this.showHelp(0)
        } else if (s != s0) {
          bRepass = 0
          $this.showHelp(4, '两次输入密码不一致')
        } else {
          bRepass = 1
          $this.showHelp(2, '好的!')
        }
      }
  })

})