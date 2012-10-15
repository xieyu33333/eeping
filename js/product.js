"use strict"
var StarEval = function (expr) {
  this.current = 0
  this.$holder = $(expr)
    .on('mouseleave', $.proxy(function () {
      this.shineTo(this.current)
    }, this))
    .on('mouseenter', 'li', function () {
      $(this).prevAll().andSelf().attr('class', 'starred')
      $(this).nextAll().attr('class', 'unstarred')
    })
  this.$stars = this.$holder.children()
}
StarEval.prototype = {
    constructor: StarEval
  , shineTo: function (i) {
      this.$stars.slice(0, i).attr('class', 'starred')
      this.$stars.slice(i).attr('class', 'unstarred')
    }
  , assign: function (i) {
      this.current = i
      this.shineTo(this.current)
    }
}

$(function () {

  "use strict"

  var REVIEW_PER_LOAD = 20, LOAD_PER_PAGE = 3
    , panel, rid, mode1 = 0, mode2 = 0, totalpage = 0, page = 0, autoload = 0
    , interval, working = new Array()
    , mine = new Array()
    , s62l62 = new Array('#6262da', '#629eda', '#62dada', '#62da9e', '#62da62', '#9eda62', '#dada62', '#da9e62', '#da6262', '#da629e', '#da62da', '#9e62da')

    , $nav = $('.rankname')
        .click(function () {
          var $this = $(this)
          if ($this.is('.active')) return
          
          $('.add-on-nav .active').removeClass('active')

          rid = $this.index()
          $this.siblings().removeClass('active')
          mine[rid] ? initPanel(mine[rid][0], mine[rid][1]) : initPanel()
          initPages()
        })

    , $panel = $('.review-panel')
        .on('click', '.btn-primary', function () {
          $txtp.hide()
          $txta.height($txtp.height()).show().val($txtp.html()).focus()
          $btn0.html('发 布')
        })
        .on('click', '.btn-success', function () {
          var mod = $txta.val()
          if (working['postReview'] === true) return
          if (mod == $txtp.html()) return slideUp()
          $.ajax({
              data: {type:'postReview', uid:uid, auth:auth, pid:pid, rid:rid, text:mod}
            , success: function (r) {
                if (r[0] == 0) return msg(4, r[1])
                msg(2, '发布成功')
                slideUp(mod)
              }
          })
        })
        .on('click', '.btn-cancle', function () {
          slideUp()
        })
        .on('click', '.btn-danger', function () {
          var c = confirm('确认要删除星级和评论内容么？')
          c == true && $.ajax({
              data: {type:'delete', uid:uid, auth:auth, pid:pid, rid:rid}
            , success: function (r) {
                if (r[0] == 0) return msg(4, r[1])
                msg(2, '已删除')
                mine[rid] = initPanel()
              }
          })
        })

    , starEval = new StarEval('.stars')
    , $txtp = $panel.find('.text-published')

    , $txta = $panel.find('textarea')
        .focus(function () {
          $(this).animate({height:'108px'}, 200)
          $btn1.attr('class', 'btn btn-cancle').html('取消')
        }).blur(function () {
          $(this).val() || $btn1.click()
        })

    , $ct = $panel.find('.textarea-counter')
    , $btn0 = $panel.find('button:first')
    , $btn1 = $panel.find('button:last')

    , $stmt = $('.review-stream .stream-title')
    , $byuse=$('#byuse'), $bytime=$('#bytime')
    , $byrank = $('#byrank')
    , $bypage = $('#bypage'), $pagestat = $('#pagestat'), $pagelist = $('#pagelist'), $nextpage = $('#nextpage')
    , $sf = $('.review-stream .stream-footer')

    , $stmc = $('.review-stream .stream-content')
        .on('click', '.reply', function () {
          if (uid == '') return $('[href="#signin"]').click()
          $('.reply-active').click()
          $(this).attr('class', 'reply-active')
          $('\
            <div class="textarea-holder">\
              <textarea></textarea>\
              <div class="textarea-counter"></div>\
              <div class="textarea-btn-holder">\
                <div class="btn disabled">发布回复</div>\
                <div class="btn btn-cancle">取消</div>\
              </div>\
            </div>').insertAfter($(this)).slideDown(200)
        })
        .on('click', '.reply-active', function () {
          $(this).attr('class', 'reply').next().slideUp(200, function () {
            $(this).remove()
          })
        })
        .on('click', '.btn-cancle', function () {
          $(this).parent().parent().prev().click()
        })
        .on('click', '.btn-success', function () {
          var $this = $(this)
            , $node = $this.closest('.node')
            , comment = $this.parent().prev().prev().val()
            , par_id = $node.parent().attr('id')
            , root_id = $this.closest('.root').attr('id')
            , level = parseInt($node.attr('level')) + 1
          if (working['postComment'] == true) return
          $.ajax({
              data: {type:'postComment', uid:uid, auth:auth, root_id:root_id, par_id:par_id, level:level, comment:comment}
            , success: function (r) {
                var $el = $('.prototype').clone().attr('class', 'subtree')
                if (r[0] == 0) return msg(4, r[1])
                msg(2, '发布成功')
                $('.reply-active').click()
                $el.attr('id', r[1].id)
                  .find('.node').attr('level', level)
                $el.find('.node-link').attr('href', '/user/' + uid)
                  .find('img').attr('src', avatar)
                  .next().html(name)
                $el.find('.node-time').html(r[1].time.slice(0, 16))
                $el.find('.node-text').html(comment)
                $el.find('.node-star').remove()
                $el.insertAfter($node).slideDown(200)
              }
          })
        })
        .on('click', '.vote button', function () {
          var $this = $(this)
            , $a = $this.children('div')
            , $b = $this.siblings().children('div')
            , v = $this.siblings().is('.active')
            , id = $this.closest('.node').parent().attr('id')
            , type = $this.index() == 0 ? 'postLike' : 'postDislike'
          if (uid == '') return $('[href="#signin"]').click()
          if (working[type] == true || $this.is('.active')) return
          $this.addClass('active').siblings().removeClass('active')
          $.ajax({
              data: {type:type, uid:uid, auth:auth, id:id}
            , success: function (r) {
                if (r[0] == 0) return msg(4, r[1])
                msg(2, '非常感谢')
                $a.html(r[1])
                v && $b.html($b.html() - 1)
              }
          })
        })

    , $bugStrm = $('#bug-report .stream-content')
        .on('click', '.btn-cancle', function () {
          var $n = $(this).closest('.node')
          $n.slideUp(200, function () {
            $n.remove()
          })
          $('#submit-bug').removeClass('active')
        })
        .on('click', '.btn-success', function () {
          var $this = $(this)
            , s = $this.parent().prev().prev().val()
          $.ajax({
              data: {type:'postBug', uid:uid, auth:auth, pid:pid, bug:s}
            , success: function (r) {
                var $el = $('.prototype').clone().attr('class', 'root')
                if (r[0] == 0) return msg(4, r[1])
                msg(2, '发布成功')
                $this.next().click()
                $el.attr('id', r[1].id)
                $el.find('.node-link').attr('href', '/user/' + uid)
                  .find('img').attr('src', avatar)
                  .next().html(name)
                $el.find('.node-time').html(r[1].time.slice(0, 16))
                $el.find('.node-text').html(s)
                $el.find('.node-star, .reply').remove()
                $el.prependTo($bugStrm).slideDown(200)
              }
          })
        })
        .on('click', '.vote button', function () {
          var $this = $(this)
            , $a = $this.children('div')
            , $b = $this.siblings().children('div')
            , v = $this.siblings().is('.active')
            , id = $this.closest('.node').parent().attr('id')
            , type = $this.index() == 0 ? 'postLike' : 'postDislike'
          if (uid == '') return $('[href="#signin"]').click()
          if (working[type] == true || $this.is('.active')) return
          $this.addClass('active').siblings().removeClass('active')
          $.ajax({
              data: {type:type, uid:uid, auth:auth, id:id}
            , success: function (r) {
                if (r[0] == 0) return msg(4, r[1])
                msg(2, '非常感谢')
                $a.html(r[1])
                v && $b.html($b.html() - 1)
              }
          })
        })

  function initPanel(score, text) {
    starEval.assign(score ? score : 0)
    score ? $txta.removeAttr('disabled') : $txta.attr('disabled', 'true')
    $btn1.attr('class', 'btn ' + (score ? 'btn-danger' : 'disabled')).html('删除')
    $txta.val('').height(20)[text ? 'hide' : 'show']()
    $txtp[text ? 'show' : 'hide']().html(text ? text : '')
    $btn0.attr('class', 'btn ' + (text ? 'btn-primary' : 'disabled')).html(text ? '修 改' : '发 布')
    return [score, text]
  }
  function slideUp(newTxt) {
    var h = newTxt ? $txtp.html(newTxt).height() : $txtp.height()
      , text = $txtp.html()
    $txta.animate({height: h > 30 ? h : 30}, 200, function () {
      $txta.val('')
      text && $txta.hide() && $txtp.show()
      $btn0.attr('class', 'btn ' + (text ? 'btn-primary' : 'disabled')).html(text ? '修 改' : '发 布')
      $btn1.attr('class', 'btn btn-danger').html('删除')
    })
  }

  function makeNode(el, r, v) {
    el.attr('id', v.id).css('display', 'none')
    el.find('.node').attr('level', v.level ? v.level : 1)
    el.find('.node-link').attr('href', r['user'][v.uid].domain ? '/user/' + r['user'][v.uid].domain : '/user/' + v.uid)
    el.find('.review-avatar').attr('src', r['user'][v.uid].avatar)
    el.find('.node-name').html(r['user'][v.uid].name)
    el.find('.node-time').html(v.time.slice(0,16))
    el.find('.node-text').html(v.text)
    el.find('.like-data').html(v.like)
    el.find('.dislike-data').html(v.dislike)
    el.find('.unstarred-tiny').slice(0, v.score).attr('class', 'starred-tiny')
  }

  function initPages() {
    page = 0
    totalpage = 0
    autoload = 0
    $pagestat.html('第1页')
    $pagelist.empty()
    $nextpage.hide()
    $stmc.empty()
    $.ajax({
        data: {type:'getRowNum', uid:uid, auth:auth, pid:pid, rid:rid, mode1:mode1}
      , success: function (r) {
          var i
          if (r[0] = 0) return msg(4, r[1])
          totalpage = Math.ceil(r[1] / (REVIEW_PER_LOAD * LOAD_PER_PAGE))
          totalpage > 1 && $nextpage.show()
          if (totalpage == 0) {
            autoload = LOAD_PER_PAGE - 1
            $pagelist.append('<div class="pageopt">第1页</div>')
          } else {
            for (i = 0; i < totalpage; i++) {
              $pagelist.append('<div class="pageopt">第' + (i + 1) + '页</div>')
            }
            getReviews(0, REVIEW_PER_LOAD)
          }
      }
    })
  }
  function getReviews (start, length) {
    $.ajax({
        data: {type:'getReview', uid:uid, auth:auth, pid:pid, rid:rid, mode1:mode1, mode2:mode2, limit0:start, limit1:length}
      , success: function (r) {
          var i, depth = 1
          if (r[0] == 0) return msg(4, r[1])

          $.each(r['root'], function (k, v) {
            var $el = $('.prototype').clone().attr('class', 'root')
            makeNode($el, r, v)
            $el.appendTo($stmc).fadeIn(200)
          })
          if (!r['children']) return
          $.each(r['children'], function (k, v) {
            depth = Math.max(depth, v.level)
          })
          for (i = 2; i <= depth; i++) {
            $.each(r['children'], function (k, v) {
              var $el = $('.prototype').clone().attr('class', 'subtree')
              if (v.level == i) {
                $el.find('.node-star').remove()
                makeNode($el, r, v)
                $el.appendTo('#'+v.par_id).fadeIn(200)
              }
            })
          }
          if (!r['vote']) return
          $.each(r['vote'], function (k, v) {
            $('#' + k).children('.node').find('button:' + (v == 't' ? 'first' : 'last')).addClass('active')
          })
          r['root'].length < length && (autoload = LOAD_PER_PAGE - 1)
          $sf.css('background', '')
        }
    })
  }

  $.ajaxSetup({url:'/php/product_ajax.php', type:'post', dataType:'json', timeout:7000})

  $('body')
    .ajaxSend(function (e, x, s) {
      switch (s.data.split('&')[0]) {
        case 'type=postScore':
          msg(3, '正在提交...')
          working['postScore'] = true
          break
        case 'type=postReview':
          msg(3, '正在提交...')
          working['postReview'] = true
          break
        case 'type=delete':
          msg(3, '请稍候...')
          break
        case 'type=postComment':
          msg(3, '正在提交...')
          working['postComment'] = true
          break
        case 'type=postLike':
          msg(3, '请稍候...')
          working['postLike'] == true
          break
        case 'type=postDislike':
          msg(3, '请稍候...')
          working['postDislike'] == true
          break
        case 'type=postSpecVote':
          msg(3, '正在提交...')
          break
        default:
          msg(1, '正在加载...')
          break
      }
    })
    .ajaxComplete(function (e, x, s) {
      
    })
    .ajaxStart(function () {
      
    })
    .ajaxStop(function () {
      
    })
    .ajaxError(function () {
      msg(4, '发生错误，请稍候重试...')
    })
    .ajaxSuccess(function (e, x, s) {
      msg(0)
      switch (s.data.split('&')[0]) {
        case 'type=postScore':
          working['postScore'] = false
          break
        case 'type=postReview':
          working['postReview'] = false
          break
        case 'type=postComment':
          working['postComment'] = false
          break
        case 'type=postLike':
          working['postLike'] == false
          break
        case 'type=postDislike':
          working['postDislike'] == false
          break
      }
    })

  $('#gallery')
    .on('click', '.gallery-thumb img', function () {
      var $this = $(this)
        , $gi = $('.gallery-image')
        , i = $this.index() + 1
      if ($this.is('.active')) return
      $this.addClass('active').siblings('.active').removeClass('active')
      $gi.children().hide()
      if ($('#img' + i).length) {
        $('#img' + i).fadeIn(400)
      } else {
        $('<img id="img' + i + '" src="/img/' + pid + '/' + pid + i + '.png" title="点击查看下一张">').appendTo($gi).fadeIn(400)
      }
    })
    .on('click', '.gallery-image img', function () {
      var i = parseInt($(this).attr('id').slice(3))
      $('.gallery-thumb img').eq(i).click()
    })

  $('.show-gallery').click(function () {
    $('.gallery-thumb img:eq(0)').click()
  })

  $('#spec, #desc').bind({
      mouseenter: function () {
        $(this).children(':eq(0)').fadeIn(200)
      }
    , mouseleave: function () {
        $(this).children(':eq(0)').hide()
      }
  })

  $('#btn-edit-spec').bind({
      click: function () {
        if ($('#spec-editor').find('.spec-editor-cd').length) return
        $.ajax({
            data: {type:'getSpecCandidates', uid:uid, auth:auth, pid:pid}
          , success: function (r) {
              var i = 0;
              if (r[0] == 0) return msg(4, r[1])
              r != '' && $.each(r[1], function (k, v) {
                $('\
                <button class="btn btn-mini spec-editor-cd" data-id="'+v.id+'">'+v.content+'\
                  <div class="colorful" style="height:'+Math.round(Math.log(v.like + 1) / Math.log(1.2))+'px;background-color:'+s62l62[i]+';">\
                    <div class="score">'+v.like+'</div>\
                  </div>\
                </button>').appendTo('#'+v.spec_id+' .btn-group')
                i ++;
                i == 12 && (i = 0)
              })
              // $('#spec-editor').find('.btn-group .btn:first-child').addClass('active')
            }
        })
      }
  })

  $('#spec-editor')
    .on('click', '.btn-group > .btn', function () {
      $(this).is('.active') ? $(this).removeClass('active') : $(this).addClass('active').siblings('.active').removeClass('active')
    })
    .on('click', '.modal-footer > .btn-primary', function () {
      var ids = ''
        , $cancle = $(this).next()
      if (!$('#spec-editor .active').length) return $cancle.click()
      $.each($('#spec-editor').find('.active'), function (k, v) {
        ids += $(v).attr('data-id')
      })
      $.ajax({
          data: {type:'postSpecVote', uid:uid, auth:auth, candidate_id:ids}
        , success: function (r) {
            if (r[0] == 0) return msg(4, r[1])
            msg(2, '非常感谢')
            $cancle.click()
          }
      })
    })
    .on('mouseenter', '.spec-editor-line', function () {
      $(this).find('.btn-simple').fadeIn(200)
    })
    .on('mouseleave', '.spec-editor-line', function () {
      $(this).find('.btn-simple').hide()
    })



  $('.bug-report').click(function () {
    if ($(this).is('.active')) return
    $('.rankname.active').removeClass('active')
    $bugStrm.empty()
    $.ajax({
        data: {type:'getBug', uid:uid, auth:auth, pid:pid}
      , success: function (r) {
          if (r[0] == 0) return msg(4, r[1])
          $.each(r['node'], function (k, v) {
            var $el = $('.prototype').clone().attr('class', 'root')
            $el.attr('id', v.id).css('display', 'none')
            $el.find('.node-link').attr('href', r['user'][v.uid].domain ? '/user/' + r['user'][v.uid].domain : '/user/' + v.uid)
            $el.find('.review-avatar').attr('src', r['user'][v.uid].avatar)
            $el.find('.node-name').html(r['user'][v.uid].name)
            $el.find('.node-time').html(v.time.slice(0,16))
            $el.find('.node-text').html(v.text)
            $el.find('.like-data').html(v.like)
            $el.find('.dislike-data').html(v.dislike)
            $el.find('.reply, .node-star').remove()
            $el.appendTo($bugStrm).fadeIn(200)
          })
          if (!r['vote']) return
          $.each(r['vote'], function (k, v) {
            $('#' + k).children('.node').find('button:' + (v == 't' ? 'first' : 'last')).addClass('active')
          })
        }
    })
  })

  $('#submit-bug').click(function () {
    if ($(this).is('.active')) return
    $(this).addClass('active')
    $('\
      <div class="node" style="display:none;">\
        <div class="textarea-holder">\
          <textarea></textarea>\
          <div class="textarea-counter"></div>\
          <div class="textarea-btn-holder">\
            <div class="btn disabled">发布</div>\
            <div class="btn btn-cancle">取消</div>\
          </div>\
        </div>\
      </div>').prependTo($bugStrm).slideDown(200)
  })



  $('.main').on('focus', '.textarea-holder textarea', function () {
    interval = setInterval($.proxy(function () {
      var $this = $(this)
        , $ct = $this.next()
        , $btn = $this.next().next().children(':first')
        , l = $this.val().replace(/[^\x00-\xff]/g, 'a').length
      if (l == 0) {
        $btn.attr('class', 'btn disabled')
        $ct.html('还能输入<span class="green">' + 140 + '</span>字')
      } else if (l > 0 && l <= 140) {
        $btn.attr('class', 'btn btn-success')
        $ct.html('还能输入<span class="green">' + (140 - l) + '</span>字')
      } else if (l > 140) {
        $btn.attr('class', 'btn disabled')
        $ct.html('已经超出<span class="red">' + (l - 140) + '</span>字')
      }
    }, this), 100)
  }).on('blur', '.textarea-holder textarea', function () {
    clearInterval(interval)
    $(this).next().empty()
  })

  starEval.$stars.click(function () {
    var score = $(this).index() + 1
    if (uid == '') return $('[href="#signin"]').click()
    if (score == starEval.current || working['postScore'] == true) return
    $.ajax({
        data: {type:'postScore', uid:uid, auth:auth, pid:pid, rid:rid, score:score}
      , success: function (r) {
          if (r[0] == 0) return msg(4, r[1])
          msg(2, '评分成功')
          mine[rid] ? (mine[rid][0] = score) : mine[rid] = [score, '']
          starEval.assign(score)
          $txta.is(':disabled') && $txta.removeAttr('disabled') && $btn1.attr('class', 'btn btn-danger')
        }
    })
  })

  setTimeout(function() {
    uid != '' ? $.ajax({
        data: {type:'getMine', uid:uid, auth:auth, pid:pid}
      , success: function (r) {
          if (r[0] == 0) return msg(4, r[1])
          r[1] && $.each(r[1], function (k, v) {
            mine[v.rid] = [v.score, v.text]
          })
          $nav.eq(0).click().addClass('active')
        }
    }) : $nav.eq(0).click().addClass('active')
  }, 10)

  $byuse.click(function () {
    $bytime.removeClass('active')
    $(this).addClass('active')
    mode2 = 0
    initPages()
  })
  $bytime.click(function () {
    $byuse.removeClass('active')
    $(this).addClass('active')
    mode2 = 1
    initPages()
  })
  $byrank.click(function () {
    $('#ranklist').show()
    setTimeout(function() {
      $('body').one('click', function () {
        $('#ranklist').fadeOut(200)
      })
    }, 10)
  })
  $('.rankopt').click(function () {
    var stat = $('#rankstat').html(), request = $(this).html()
    if (stat != request) {
      $('#rankstat').html(request)
      mode1 = $(this).index()
      initPages()
    }
  })
  $bypage.click(function () {
    $pagelist.show()
    setTimeout(function () {
      $('body').one('click', function () {
        $pagelist.fadeOut(200)
      })
    }, 10)
  })
  $('.pageopt').live('click', function () {
    var stat = $pagestat.html(), request = $(this).html()
    if (stat != request) {
      $pagestat.html(request)
      autoload = 0
      page = $(this).index()
      if (page + 1 == totalpage) {
        $nextpage.hide()
      } else {
        $nextpage.show()
      }
      $stmc.empty()
      getReviews(REVIEW_PER_LOAD * LOAD_PER_PAGE * page, REVIEW_PER_LOAD)
    }
  })
  $nextpage.click(function () {
    $('.pageopt').eq(page + 1).click()
  })


  

  // $(window).scroll(function () {
  //   var h = document.body.scrollHeight
  //     , h1 =  window.innerHeight
  //     , h2 = document.body.scrollTop
  //   if (h2 == h - h1 && h2 != 0 && autoload < (LOAD_PER_PAGE -1)) {
  //     $('.stream-footer').css('background', 'url(/img/loading.gif) no-repeat center center')
  //     autoload ++
  //     getReviews(REVIEW_PER_LOAD * (LOAD_PER_PAGE * page + autoload), REVIEW_PER_LOAD)
  //   }
  // })
})