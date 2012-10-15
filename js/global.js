$(function () {
  var $mail = $('#mail')
    , $password = $('#password')
  $('#signin-btn').click(function () {
  	var mail = $mail.val()
      , password = $password.val()
  	if ( !! mail && !! password) {
  		return
  	} else {
  		return false
  	}
  })
  $('#signout').click(function () {
    $('#signout-form').submit()
  })
})

var msgT
function msg(mode, string, time) {
  var $msg
  document.getElementById('msg-area') || $('<div id="msg-area"></div>').appendTo(document.body)
  $msg = $('#msg-area').hide()
  string && $msg.html(string).css('left',($(window).width() - $msg.width()) / 2)
  switch (mode) {
    case 1:
      clearTimeout(msgT)
      $msg.attr('class', 'msg-process').fadeIn(200)
      break
    case 2:
      clearTimeout(msgT)
      $msg.attr('class', 'msg-success').fadeIn(200)
      msgT = setTimeout(function() {
        $msg.fadeOut(600)
      }, time ? time : 4000)
      break
    case 3:
      clearTimeout(msgT)
      $msg.attr('class', 'msg-warning').fadeIn(200)
      msgT = setTimeout(function() {
        $msg.fadeOut(600)
      }, time ? time : 4000)
      break
    case 4:
      clearTimeout(msgT)
      $msg.attr('class', 'msg-error').fadeIn(200)
      msgT = setTimeout(function() {
        $msg.fadeOut(600)
      }, time ? time : 4000)
      break
    case 0:
      $msg.is('.msg-success') || $msg.is('.msg-warning') || $msg.is('.msg-error') || $msg.fadeOut(200)
      break
  }
}

(function( $ ){
  "use strict"
  var Modal = function ( content ) {
    this.$element = $(content)
      .on('click.dismiss.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))
  }
  Modal.prototype = {
      constructor: Modal
    , toggle: function () {
        return this[!this.isShown ? 'show' : 'hide']()
      }
    , show: function () {
        var that = this
        if (this.isShown) return
        $('body').addClass('modal-open')
        this.isShown = true
        escape.call(this)
        backdrop.call(this, function () {
          !that.$element.parent().length && that.$element.appendTo(document.body)
          that.$element
            .show()
        })
      }
    , hide: function ( e ) {
        e && e.preventDefault()
        if (!this.isShown) return
        var that = this
        this.isShown = false
        $('body').removeClass('modal-open')
        escape.call(this)
        backdrop.call(this)
        this.$element
          .hide()
      }
  }
  function backdrop( callback ) {
    var that = this
    if (this.isShown) {
      this.$backdrop = $('<div class="modal-backdrop" />')
        .appendTo(document.body)
      this.$backdrop.click($.proxy(this.hide, this))
      callback()
    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.remove()
      this.$backdrop = null
    }
  }
  function escape() {
    var that = this
    if (this.isShown) {
      $(document).on('keyup.dismiss.modal', function ( e ) {
        e.which == 27 && that.hide()
      })
    } else if (!this.isShown) {
      $(document).off('keyup.dismiss.modal')
    }
  }
  $.fn.modal = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('modal')
      if (!data) $this.data('modal', (data = new Modal(this)))
      data[option]()
    })
  }
  $.fn.modal.Constructor = Modal
  $(function () {
    $('body').on('click.modal.data-api', '[data-show="modal"]', function ( e ) {
      var $this = $(this), href
        , $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, ''))
      e.preventDefault()
      $target.modal('show')
    })
  })
})( window.jQuery );

(function( $ ){
  "use strict"
  $(function () {
    var toggle = '[data-toggle="dropdown"]'
    $('html').on('click.dropdown.data-api', function () {
      $(toggle).parent().removeClass('open')
    })
    $('html').on('click.dropdown.data-api', '[data-stop="dropdown"]', function (e) {
      e.target && e.target.nodeName != 'A' && e.stopPropagation()
    })
    $('body').on('click.dropdown.data-api', toggle, function (e) {
      var $this = $(this), href
        , $parent = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, ''))
        , isActive
      $parent.length || ($parent = $this.parent())
      $parent.toggleClass('open')
      return false
    })
  })
})( window.jQuery );

(function( $ ){
  "use strict"
  var Tab = function ( element ) {
    this.element = $(element)
  }
  Tab.prototype = {
    constructor: Tab
  , show: function () {
      var $this = this.element, href
        , $ul = $this.closest('ul:not(.dropdown-menu)')
        , $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, ''))
      if ( $this.parent('li').hasClass('active') ) return
      this.activate($this.parent('li'), $ul)
      this.activate($target, $target.parent())
    }
  , activate: function (element, container) {
      container.find('> .active').removeClass('active')
        .find('> .dropdown-menu > .active').removeClass('active')
      element.addClass('active')
      if (element.parent('.dropdown-menu')) {
        element.closest('li.dropdown').addClass('active')
      }
    }
  }
  $.fn.tab = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('tab')
      if (!data) $this.data('tab', (data = new Tab(this)))
      data[option]()
    })
  }
  $.fn.tab.Constructor = Tab
  $(function () {
    $('body').on('click.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
      e.preventDefault()
      $(this).tab('show')
    })
  })
})( window.jQuery );

(function( $ ){
  "use strict"
  var Button = function ( element, options ) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.button.defaults, options)
  }
  Button.prototype = {
      constructor: Button
    , setState: function ( state ) {
        var d = 'disabled'
          , $el = this.$element
          , data = $el.data()
          , val = $el.is('input') ? 'val' : 'html'
        state = state + 'Text'
        data.resetText || $el.data('resetText', $el[val]())
        $el[val](data[state] || this.options[state])
        // push to event loop to allow forms to submit
        setTimeout(function () {
          state == 'loadingText' ?
            $el.addClass(d).attr(d, d) :
            $el.removeClass(d).removeAttr(d)
        }, 0)
      }
    , toggle: function () {
        var $parent = this.$element.parent('[data-toggle="buttons-radio"]')
        $parent && $parent
          .find('.active')
          .removeClass('active')
        this.$element.toggleClass('active')
      }
  }
  $.fn.button = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('button')
        , options = typeof option == 'object' && option
      if (!data) $this.data('button', (data = new Button(this, options)))
      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }
  $.fn.button.defaults = {
    loadingText: 'loading...'
  }
  $.fn.button.Constructor = Button
  $(function () {
    $('body').on('click.button.data-api', '[data-toggle^=button]', function ( e ) {
      var $btn = $(e.target)
      if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
      $btn.button('toggle')
    })
  })
})( window.jQuery );