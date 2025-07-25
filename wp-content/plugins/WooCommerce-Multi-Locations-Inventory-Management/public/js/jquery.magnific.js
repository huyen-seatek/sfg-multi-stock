!(function (e) {
  "function" == typeof define && define.amd
    ? define(["jquery"], e)
    : e(
        "object" == typeof exports
          ? require("jquery")
          : window.jQuery || window.Zepto
      );
})(function (e) {
  var t,
    i,
    n,
    o,
    r,
    a,
    s = "Close",
    l = "BeforeClose",
    c = "MarkupParse",
    p = "Open",
    d = "Change",
    u = ".mfp",
    f = "mfp-ready",
    m = "mfp-removing",
    g = "mfp-prevent-close",
    h = function () {},
    v = !!window.jQuery,
    $ = e(window),
    y = function (e, i) {
      t.ev.on("mfp" + e + u, i);
    },
    C = function (t, i, n, o) {
      var r = document.createElement("div");
      return (
        (r.className = "mfp-" + t),
        n && (r.innerHTML = n),
        o ? i && i.appendChild(r) : ((r = e(r)), i && r.appendTo(i)),
        r
      );
    },
    b = function (i, n) {
      t.ev.triggerHandler("mfp" + i, n),
        t.st.callbacks &&
          ((i = i.charAt(0).toLowerCase() + i.slice(1)),
          t.st.callbacks[i] &&
            t.st.callbacks[i].apply(t, e.isArray(n) ? n : [n]));
    },
    w = function (i) {
      return (
        (i === a && t.currTemplate.closeBtn) ||
          ((t.currTemplate.closeBtn = e(
            t.st.closeMarkup.replace("%title%", t.st.tClose)
          )),
          (a = i)),
        t.currTemplate.closeBtn
      );
    },
    x = function () {
      e.magnificPopup.instance ||
        ((t = new h()).init(), (e.magnificPopup.instance = t));
    },
    I = function () {
      var e = document.createElement("p").style,
        t = ["ms", "O", "Moz", "Webkit"];
      if (void 0 !== e.transition) return !0;
      for (; t.length; ) if (t.pop() + "Transition" in e) return !0;
      return !1;
    };
  (h.prototype = {
    constructor: h,
    init: function () {
      var i = navigator.appVersion;
      (t.isLowIE = t.isIE8 = document.all && !document.addEventListener),
        (t.isAndroid = /android/gi.test(i)),
        (t.isIOS = /iphone|ipad|ipod/gi.test(i)),
        (t.supportsTransition = I()),
        (t.probablyMobile =
          t.isAndroid ||
          t.isIOS ||
          /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(
            navigator.userAgent
          )),
        (n = e(document)),
        (t.popupsCache = {});
    },
    open: function (i) {
      if (!1 === i.isObj) {
        (t.items = i.items.toArray()), (t.index = 0);
        var o,
          a,
          s = i.items;
        for (o = 0; o < s.length; o++)
          if (((a = s[o]).parsed && (a = a.el[0]), a === i.el[0])) {
            t.index = o;
            break;
          }
      } else
        (t.items = e.isArray(i.items) ? i.items : [i.items]),
          (t.index = i.index || 0);
      if (t.isOpen) {
        t.updateItemHTML();
        return;
      }
      (t.types = []),
        (r = ""),
        i.mainEl && i.mainEl.length ? (t.ev = i.mainEl.eq(0)) : (t.ev = n),
        i.key
          ? (t.popupsCache[i.key] || (t.popupsCache[i.key] = {}),
            (t.currTemplate = t.popupsCache[i.key]))
          : (t.currTemplate = {}),
        (t.st = e.extend(!0, {}, e.magnificPopup.defaults, i)),
        (t.fixedContentPos =
          "auto" === t.st.fixedContentPos
            ? !t.probablyMobile
            : t.st.fixedContentPos),
        t.st.modal &&
          ((t.st.closeOnContentClick = !1),
          (t.st.closeOnBgClick = !1),
          (t.st.showCloseBtn = !1),
          (t.st.enableEscapeKey = !1)),
        t.bgOverlay ||
          ((t.bgOverlay = C("bg").on("click" + u, function () {
            t.close();
          })),
          (t.wrap = C("wrap")
            .attr("tabindex", -1)
            .on("click" + u, function (e) {
              t._checkIfClose(e.target) && t.close();
            })),
          (t.container = C("container", t.wrap))),
        (t.contentContainer = C("content")),
        t.st.preloader &&
          (t.preloader = C("preloader", t.container, t.st.tLoading));
      var l = e.magnificPopup.modules;
      for (o = 0; o < l.length; o++) {
        var d = l[o];
        t["init" + (d = d.charAt(0).toUpperCase() + d.slice(1))].call(t);
      }
      b("BeforeOpen"),
        t.st.showCloseBtn &&
          (t.st.closeBtnInside
            ? (y(c, function (e, t, i, n) {
                i.close_replaceWith = w(n.type);
              }),
              (r += " mfp-close-btn-in"))
            : t.wrap.append(w())),
        t.st.alignTop && (r += " mfp-align-top"),
        t.fixedContentPos
          ? t.wrap.css({
              overflow: t.st.overflowY,
              overflowX: "hidden",
              overflowY: t.st.overflowY,
            })
          : t.wrap.css({ top: $.scrollTop(), position: "absolute" }),
        (!1 !== t.st.fixedBgPos &&
          ("auto" !== t.st.fixedBgPos || t.fixedContentPos)) ||
          t.bgOverlay.css({ height: n.height(), position: "absolute" }),
        t.st.enableEscapeKey &&
          n.on("keyup" + u, function (e) {
            27 === e.keyCode && t.close();
          }),
        $.on("resize" + u, function () {
          t.updateSize();
        }),
        t.st.closeOnContentClick || (r += " mfp-auto-cursor"),
        r && t.wrap.addClass(r);
      var m = (t.wH = $.height()),
        g = {};
      if (t.fixedContentPos && t._hasScrollBar(m)) {
        var h = t._getScrollbarSize();
        h && (g.marginRight = h);
      }
      t.fixedContentPos &&
        (t.isIE7
          ? e("body, html").css("overflow", "hidden")
          : (g.overflow = "hidden"));
      var v = t.st.mainClass;
      return (
        t.isIE7 && (v += " mfp-ie7"),
        v && t._addClassToMFP(v),
        t.updateItemHTML(),
        b("BuildControls"),
        e("html").css(g),
        t.bgOverlay.add(t.wrap).prependTo(t.st.prependTo || e(document.body)),
        (t._lastFocusedEl = document.activeElement),
        setTimeout(function () {
          t.content
            ? (t._addClassToMFP(f), t._setFocus())
            : t.bgOverlay.addClass(f),
            n.on("focusin" + u, t._onFocusIn);
        }, 16),
        (t.isOpen = !0),
        t.updateSize(m),
        b(p),
        i
      );
    },
    close: function () {
      t.isOpen &&
        (b(l),
        (t.isOpen = !1),
        t.st.removalDelay && !t.isLowIE && t.supportsTransition
          ? (t._addClassToMFP(m),
            setTimeout(function () {
              t._close();
            }, t.st.removalDelay))
          : t._close());
    },
    _close: function () {
      b(s);
      var i = m + " " + f + " ";
      if (
        (t.bgOverlay.detach(),
        t.wrap.detach(),
        t.container.empty(),
        t.st.mainClass && (i += t.st.mainClass + " "),
        t._removeClassFromMFP(i),
        t.fixedContentPos)
      ) {
        var o = { marginRight: "" };
        t.isIE7 ? e("body, html").css("overflow", "") : (o.overflow = ""),
          e("html").css(o);
      }
      n.off("keyup" + u + " focusin" + u),
        t.ev.off(u),
        t.wrap.attr("class", "mfp-wrap").removeAttr("style"),
        t.bgOverlay.attr("class", "mfp-bg"),
        t.container.attr("class", "mfp-container"),
        t.st.showCloseBtn &&
          (!t.st.closeBtnInside || !0 === t.currTemplate[t.currItem.type]) &&
          t.currTemplate.closeBtn &&
          t.currTemplate.closeBtn.detach(),
        t.st.autoFocusLast && t._lastFocusedEl && e(t._lastFocusedEl).focus(),
        (t.currItem = null),
        (t.content = null),
        (t.currTemplate = null),
        (t.prevHeight = 0),
        b("AfterClose");
    },
    updateSize: function (e) {
      if (t.isIOS) {
        var i = document.documentElement.clientWidth / window.innerWidth,
          n = window.innerHeight * i;
        t.wrap.css("height", n), (t.wH = n);
      } else t.wH = e || $.height();
      t.fixedContentPos || t.wrap.css("height", t.wH), b("Resize");
    },
    updateItemHTML: function () {
      var i = t.items[t.index];
      t.contentContainer.detach(),
        t.content && t.content.detach(),
        i.parsed || (i = t.parseEl(t.index));
      var n = i.type;
      if (
        (b("BeforeChange", [t.currItem ? t.currItem.type : "", n]),
        (t.currItem = i),
        !t.currTemplate[n])
      ) {
        var r = !!t.st[n] && t.st[n].markup;
        b("FirstMarkupParse", r),
          r ? (t.currTemplate[n] = e(r)) : (t.currTemplate[n] = !0);
      }
      o && o !== i.type && t.container.removeClass("mfp-" + o + "-holder");
      var a = t["get" + n.charAt(0).toUpperCase() + n.slice(1)](
        i,
        t.currTemplate[n]
      );
      t.appendContent(a, n),
        (i.preloaded = !0),
        b(d, i),
        (o = i.type),
        t.container.prepend(t.contentContainer),
        b("AfterChange");
    },
    appendContent: function (e, i) {
      (t.content = e),
        e
          ? t.st.showCloseBtn && t.st.closeBtnInside && !0 === t.currTemplate[i]
            ? t.content.find(".mfp-close").length || t.content.append(w())
            : (t.content = e)
          : (t.content = ""),
        b("BeforeAppend"),
        t.container.addClass("mfp-" + i + "-holder"),
        t.contentContainer.append(t.content);
    },
    parseEl: function (i) {
      var n,
        o = t.items[i];
      if (
        (o.tagName
          ? (o = { el: e(o) })
          : ((n = o.type), (o = { data: o, src: o.src })),
        o.el)
      ) {
        for (var r = t.types, a = 0; a < r.length; a++)
          if (o.el.hasClass("mfp-" + r[a])) {
            n = r[a];
            break;
          }
        (o.src = o.el.attr("data-mfp-src")),
          o.src || (o.src = o.el.attr("href"));
      }
      return (
        (o.type = n || t.st.type || "inline"),
        (o.index = i),
        (o.parsed = !0),
        (t.items[i] = o),
        b("ElementParse", o),
        t.items[i]
      );
    },
    addGroup: function (e, i) {
      var n = function (n) {
        (n.mfpEl = this), t._openClick(n, e, i);
      };
      i || (i = {});
      var o = "click.magnificPopup";
      (i.mainEl = e),
        i.items
          ? ((i.isObj = !0), e.off(o).on(o, n))
          : ((i.isObj = !1),
            i.delegate
              ? e.off(o).on(o, i.delegate, n)
              : ((i.items = e), e.off(o).on(o, n)));
    },
    _openClick: function (i, n, o) {
      if (
        (void 0 !== o.midClick
          ? o.midClick
          : e.magnificPopup.defaults.midClick) ||
        (2 !== i.which && !i.ctrlKey && !i.metaKey && !i.altKey && !i.shiftKey)
      ) {
        var r =
          void 0 !== o.disableOn
            ? o.disableOn
            : e.magnificPopup.defaults.disableOn;
        if (r) {
          if (e.isFunction(r)) {
            if (!r.call(t)) return !0;
          } else if ($.width() < r) return !0;
        }
        i.type && (i.preventDefault(), t.isOpen && i.stopPropagation()),
          (o.el = e(i.mfpEl)),
          o.delegate && (o.items = n.find(o.delegate)),
          t.open(o);
      }
    },
    updateStatus: function (e, n) {
      if (t.preloader) {
        i !== e && t.container.removeClass("mfp-s-" + i),
          n || "loading" !== e || (n = t.st.tLoading);
        var o = { status: e, text: n };
        b("UpdateStatus", o),
          (e = o.status),
          (n = o.text),
          t.preloader.html(n),
          t.preloader.find("a").on("click", function (e) {
            e.stopImmediatePropagation();
          }),
          t.container.addClass("mfp-s-" + e),
          (i = e);
      }
    },
    _checkIfClose: function (i) {
      if (!e(i).hasClass(g)) {
        var n = t.st.closeOnContentClick,
          o = t.st.closeOnBgClick;
        if (
          (n && o) ||
          !t.content ||
          e(i).hasClass("mfp-close") ||
          (t.preloader && i === t.preloader[0])
        )
          return !0;
        if (i === t.content[0] || e.contains(t.content[0], i)) {
          if (n) return !0;
        } else if (o && e.contains(document, i)) return !0;
        return !1;
      }
    },
    _addClassToMFP: function (e) {
      t.bgOverlay.addClass(e), t.wrap.addClass(e);
    },
    _removeClassFromMFP: function (e) {
      this.bgOverlay.removeClass(e), t.wrap.removeClass(e);
    },
    _hasScrollBar: function (e) {
      return (
        (t.isIE7 ? n.height() : document.body.scrollHeight) > (e || $.height())
      );
    },
    _setFocus: function () {
      (t.st.focus ? t.content.find(t.st.focus).eq(0) : t.wrap).focus();
    },
    _onFocusIn: function (i) {
      if (i.target !== t.wrap[0] && !e.contains(t.wrap[0], i.target))
        return t._setFocus(), !1;
    },
    _parseMarkup: function (t, i, n) {
      var o;
      n.data && (i = e.extend(n.data, i)),
        b(c, [t, i, n]),
        e.each(i, function (i, n) {
          if (void 0 === n || !1 === n) return !0;
          if ((o = i.split("_")).length > 1) {
            var r = t.find(u + "-" + o[0]);
            if (r.length > 0) {
              var a = o[1];
              "replaceWith" === a
                ? r[0] !== n[0] && r.replaceWith(n)
                : "img" === a
                ? r.is("img")
                  ? r.attr("src", n)
                  : r.replaceWith(
                      e("<img>").attr("src", n).attr("class", r.attr("class"))
                    )
                : r.attr(o[1], n);
            }
          } else t.find(u + "-" + i).html(n);
        });
    },
    _getScrollbarSize: function () {
      if (void 0 === t.scrollbarSize) {
        var e = document.createElement("div");
        (e.style.cssText =
          "width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;"),
          document.body.appendChild(e),
          (t.scrollbarSize = e.offsetWidth - e.clientWidth),
          document.body.removeChild(e);
      }
      return t.scrollbarSize;
    },
  }),
    (e.magnificPopup = {
      instance: null,
      proto: h.prototype,
      modules: [],
      open: function (t, i) {
        return (
          x(),
          ((t = t ? e.extend(!0, {}, t) : {}).isObj = !0),
          (t.index = i || 0),
          this.instance.open(t)
        );
      },
      close: function () {
        return e.magnificPopup.instance && e.magnificPopup.instance.close();
      },
      registerModule: function (t, i) {
        i.options && (e.magnificPopup.defaults[t] = i.options),
          e.extend(this.proto, i.proto),
          this.modules.push(t);
      },
      defaults: {
        disableOn: 0,
        key: null,
        midClick: !1,
        mainClass: "",
        preloader: !0,
        focus: "",
        closeOnContentClick: !1,
        closeOnBgClick: !0,
        closeBtnInside: !0,
        showCloseBtn: !0,
        enableEscapeKey: !0,
        modal: !1,
        alignTop: !1,
        removalDelay: 0,
        prependTo: null,
        fixedContentPos: "auto",
        fixedBgPos: "auto",
        overflowY: "auto",
        closeMarkup:
          '<button title="%title%" type="button" class="mfp-close">&#215;</button>',
        tClose: "Close (Esc)",
        tLoading: "Loading...",
        autoFocusLast: !0,
      },
    }),
    (e.fn.magnificPopup = function (i) {
      x();
      var n = e(this);
      if ("string" == typeof i) {
        if ("open" === i) {
          var o,
            r = v ? n.data("magnificPopup") : n[0].magnificPopup,
            a = parseInt(arguments[1], 10) || 0;
          r.items
            ? (o = r.items[a])
            : ((o = n), r.delegate && (o = o.find(r.delegate)), (o = o.eq(a))),
            t._openClick({ mfpEl: o }, n, r);
        } else
          t.isOpen && t[i].apply(t, Array.prototype.slice.call(arguments, 1));
      } else
        (i = e.extend(!0, {}, i)),
          v ? n.data("magnificPopup", i) : (n[0].magnificPopup = i),
          t.addGroup(n, i);
      return n;
    });
  var _,
    k,
    P,
    S = "inline",
    T = function () {
      P && (k.after(P.addClass(_)).detach(), (P = null));
    };
  e.magnificPopup.registerModule(S, {
    options: {
      hiddenClass: "hide",
      markup: "",
      tNotFound: "Content not found",
    },
    proto: {
      initInline: function () {
        t.types.push(S),
          y(s + "." + S, function () {
            T();
          });
      },
      getInline: function (i, n) {
        if ((T(), i.src)) {
          var o = t.st.inline,
            r = e(i.src);
          if (r.length) {
            var a = r[0].parentNode;
            a &&
              a.tagName &&
              (k || ((k = C((_ = o.hiddenClass))), (_ = "mfp-" + _)),
              (P = r.after(k).detach().removeClass(_))),
              t.updateStatus("ready");
          } else t.updateStatus("error", o.tNotFound), (r = e("<div>"));
          return (i.inlineElement = r), r;
        }
        return t.updateStatus("ready"), t._parseMarkup(n, {}, i), n;
      },
    },
  });
  var E,
    z = "ajax",
    O = function () {
      E && e(document.body).removeClass(E);
    },
    M = function () {
      O(), t.req && t.req.abort();
    };
  e.magnificPopup.registerModule(z, {
    options: {
      settings: null,
      cursor: "mfp-ajax-cur",
      tError: '<a href="%url%">The content</a> could not be loaded.',
    },
    proto: {
      initAjax: function () {
        t.types.push(z),
          (E = t.st.ajax.cursor),
          y(s + "." + z, M),
          y("BeforeChange." + z, M);
      },
      getAjax: function (i) {
        E && e(document.body).addClass(E), t.updateStatus("loading");
        var n = e.extend(
          {
            url: i.src,
            success: function (n, o, r) {
              var a = { data: n, xhr: r };
              b("ParseAjax", a),
                t.appendContent(e(a.data), z),
                (i.finished = !0),
                O(),
                t._setFocus(),
                setTimeout(function () {
                  t.wrap.addClass(f);
                }, 16),
                t.updateStatus("ready"),
                b("AjaxContentAdded");
            },
            error: function () {
              O(),
                (i.finished = i.loadError = !0),
                t.updateStatus(
                  "error",
                  t.st.ajax.tError.replace("%url%", i.src)
                );
            },
          },
          t.st.ajax.settings
        );
        return (t.req = e.ajax(n)), "";
      },
    },
  });
  var B,
    L,
    H = function (i) {
      if (i.data && void 0 !== i.data.title) return i.data.title;
      var n = t.st.image.titleSrc;
      if (n) {
        if (e.isFunction(n)) return n.call(t, i);
        if (i.el) return i.el.attr(n) || "";
      }
      return "";
    };
  e.magnificPopup.registerModule("image", {
    options: {
      markup:
        '<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>',
      cursor: "mfp-zoom-out-cur",
      titleSrc: "title",
      verticalFit: !0,
      tError: '<a href="%url%">The image</a> could not be loaded.',
    },
    proto: {
      initImage: function () {
        var i = t.st.image,
          n = ".image";
        t.types.push("image"),
          y(p + n, function () {
            "image" === t.currItem.type &&
              i.cursor &&
              e(document.body).addClass(i.cursor);
          }),
          y(s + n, function () {
            i.cursor && e(document.body).removeClass(i.cursor),
              $.off("resize" + u);
          }),
          y("Resize" + n, t.resizeImage),
          t.isLowIE && y("AfterChange", t.resizeImage);
      },
      resizeImage: function () {
        var e = t.currItem;
        if (e && e.img && t.st.image.verticalFit) {
          var i = 0;
          t.isLowIE &&
            (i =
              parseInt(e.img.css("padding-top"), 10) +
              parseInt(e.img.css("padding-bottom"), 10)),
            e.img.css("max-height", t.wH - i);
        }
      },
      _onImageHasSize: function (e) {
        e.img &&
          ((e.hasSize = !0),
          B && clearInterval(B),
          (e.isCheckingImgSize = !1),
          b("ImageHasSize", e),
          e.imgHidden &&
            (t.content && t.content.removeClass("mfp-loading"),
            (e.imgHidden = !1)));
      },
      findImageSize: function (e) {
        var i = 0,
          n = e.img[0],
          o = function (r) {
            B && clearInterval(B),
              (B = setInterval(function () {
                if (n.naturalWidth > 0) {
                  t._onImageHasSize(e);
                  return;
                }
                i > 200 && clearInterval(B),
                  3 == ++i ? o(10) : 40 === i ? o(50) : 100 === i && o(500);
              }, r));
          };
        o(1);
      },
      getImage: function (i, n) {
        var o = 0,
          r = function () {
            i &&
              (i.img[0].complete
                ? (i.img.off(".mfploader"),
                  i === t.currItem &&
                    (t._onImageHasSize(i), t.updateStatus("ready")),
                  (i.hasSize = !0),
                  (i.loaded = !0),
                  b("ImageLoadComplete"))
                : ++o < 200
                ? setTimeout(r, 100)
                : a());
          },
          a = function () {
            i &&
              (i.img.off(".mfploader"),
              i === t.currItem &&
                (t._onImageHasSize(i),
                t.updateStatus("error", s.tError.replace("%url%", i.src))),
              (i.hasSize = !0),
              (i.loaded = !0),
              (i.loadError = !0));
          },
          s = t.st.image,
          l = n.find(".mfp-img");
        if (l.length) {
          var c = document.createElement("img");
          (c.className = "mfp-img"),
            i.el &&
              i.el.find("img").length &&
              (c.alt = i.el.find("img").attr("alt")),
            (i.img = e(c).on("load.mfploader", r).on("error.mfploader", a)),
            (c.src = i.src),
            l.is("img") && (i.img = i.img.clone()),
            (c = i.img[0]).naturalWidth > 0
              ? (i.hasSize = !0)
              : c.width || (i.hasSize = !1);
        }
        return (t._parseMarkup(n, { title: H(i), img_replaceWith: i.img }, i),
        t.resizeImage(),
        i.hasSize)
          ? (B && clearInterval(B),
            i.loadError
              ? (n.addClass("mfp-loading"),
                t.updateStatus("error", s.tError.replace("%url%", i.src)))
              : (n.removeClass("mfp-loading"), t.updateStatus("ready")),
            n)
          : (t.updateStatus("loading"),
            (i.loading = !0),
            i.hasSize ||
              ((i.imgHidden = !0),
              n.addClass("mfp-loading"),
              t.findImageSize(i)),
            n);
      },
    },
  }),
    e.magnificPopup.registerModule("zoom", {
      options: {
        enabled: !1,
        easing: "ease-in-out",
        duration: 300,
        opener: function (e) {
          return e.is("img") ? e : e.find("img");
        },
      },
      proto: {
        initZoom: function () {
          var e,
            i = t.st.zoom,
            n = ".zoom";
          if (i.enabled && t.supportsTransition) {
            var o,
              r,
              a = i.duration,
              c = function (e) {
                var t = e
                    .clone()
                    .removeAttr("style")
                    .removeAttr("class")
                    .addClass("mfp-animated-image"),
                  n = "all " + i.duration / 1e3 + "s " + i.easing,
                  o = {
                    position: "fixed",
                    zIndex: 9999,
                    left: 0,
                    top: 0,
                    "-webkit-backface-visibility": "hidden",
                  },
                  r = "transition";
                return (
                  (o["-webkit-" + r] =
                    o["-moz-" + r] =
                    o["-o-" + r] =
                    o[r] =
                      n),
                  t.css(o),
                  t
                );
              },
              p = function () {
                t.content.css("visibility", "visible");
              };
            y("BuildControls" + n, function () {
              if (t._allowZoom()) {
                if (
                  (clearTimeout(o),
                  t.content.css("visibility", "hidden"),
                  !(e = t._getItemToZoom()))
                ) {
                  p();
                  return;
                }
                (r = c(e)).css(t._getOffset()),
                  t.wrap.append(r),
                  (o = setTimeout(function () {
                    r.css(t._getOffset(!0)),
                      (o = setTimeout(function () {
                        p(),
                          setTimeout(function () {
                            r.remove(), (e = r = null), b("ZoomAnimationEnded");
                          }, 16);
                      }, a));
                  }, 16));
              }
            }),
              y(l + n, function () {
                if (t._allowZoom()) {
                  if ((clearTimeout(o), (t.st.removalDelay = a), !e)) {
                    if (!(e = t._getItemToZoom())) return;
                    r = c(e);
                  }
                  r.css(t._getOffset(!0)),
                    t.wrap.append(r),
                    t.content.css("visibility", "hidden"),
                    setTimeout(function () {
                      r.css(t._getOffset());
                    }, 16);
                }
              }),
              y(s + n, function () {
                t._allowZoom() && (p(), r && r.remove(), (e = null));
              });
          }
        },
        _allowZoom: function () {
          return "image" === t.currItem.type;
        },
        _getItemToZoom: function () {
          return !!t.currItem.hasSize && t.currItem.img;
        },
        _getOffset: function (i) {
          var n,
            o = (n = i
              ? t.currItem.img
              : t.st.zoom.opener(t.currItem.el || t.currItem)).offset(),
            r = parseInt(n.css("padding-top"), 10),
            a = parseInt(n.css("padding-bottom"), 10);
          o.top -= e(window).scrollTop() - r;
          var s = {
            width: n.width(),
            height: (v ? n.innerHeight() : n[0].offsetHeight) - a - r,
          };
          return (
            (void 0 === L &&
              (L = void 0 !== document.createElement("p").style.MozTransform),
            L)
              ? (s["-moz-transform"] = s.transform =
                  "translate(" + o.left + "px," + o.top + "px)")
              : ((s.left = o.left), (s.top = o.top)),
            s
          );
        },
      },
    });
  var F = "iframe",
    A = function (e) {
      if (t.currTemplate[F]) {
        var i = t.currTemplate[F].find("iframe");
        i.length &&
          (e || (i[0].src = "//about:blank"),
          t.isIE8 && i.css("display", e ? "block" : "none"));
      }
    };
  e.magnificPopup.registerModule(F, {
    options: {
      markup:
        '<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>',
      srcAction: "iframe_src",
      patterns: {
        youtube: {
          index: "youtube.com",
          id: "v=",
          src: "//www.youtube.com/embed/%id%?autoplay=1",
        },
        vimeo: {
          index: "vimeo.com/",
          id: "/",
          src: "//player.vimeo.com/video/%id%?autoplay=1",
        },
        gmaps: { index: "//maps.google.", src: "%id%&output=embed" },
      },
    },
    proto: {
      initIframe: function () {
        t.types.push(F),
          y("BeforeChange", function (e, t, i) {
            t !== i && (t === F ? A() : i === F && A(!0));
          }),
          y(s + "." + F, function () {
            A();
          });
      },
      getIframe: function (i, n) {
        var o = i.src,
          r = t.st.iframe;
        e.each(r.patterns, function () {
          if (o.indexOf(this.index) > -1)
            return (
              this.id &&
                (o =
                  "string" == typeof this.id
                    ? o.substr(
                        o.lastIndexOf(this.id) + this.id.length,
                        o.length
                      )
                    : this.id.call(this, o)),
              (o = this.src.replace("%id%", o)),
              !1
            );
        });
        var a = {};
        return (
          r.srcAction && (a[r.srcAction] = o),
          t._parseMarkup(n, a, i),
          t.updateStatus("ready"),
          n
        );
      },
    },
  });
  var j = function (e) {
      var i = t.items.length;
      return e > i - 1 ? e - i : e < 0 ? i + e : e;
    },
    W = function (e, t, i) {
      return e.replace(/%curr%/gi, t + 1).replace(/%total%/gi, i);
    };
  e.magnificPopup.registerModule("gallery", {
    options: {
      enabled: !1,
      arrowMarkup:
        '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
      preload: [0, 2],
      navigateByImgClick: !0,
      arrows: !0,
      tPrev: "Previous (Left arrow key)",
      tNext: "Next (Right arrow key)",
      tCounter: "%curr% of %total%",
    },
    proto: {
      initGallery: function () {
        var i = t.st.gallery,
          o = ".mfp-gallery";
        if (((t.direction = !0), !i || !i.enabled)) return !1;
        (r += " mfp-gallery"),
          y(p + o, function () {
            i.navigateByImgClick &&
              t.wrap.on("click" + o, ".mfp-img", function () {
                if (t.items.length > 1) return t.next(), !1;
              }),
              n.on("keydown" + o, function (e) {
                37 === e.keyCode ? t.prev() : 39 === e.keyCode && t.next();
              });
          }),
          y("UpdateStatus" + o, function (e, i) {
            i.text && (i.text = W(i.text, t.currItem.index, t.items.length));
          }),
          y(c + o, function (e, n, o, r) {
            var a = t.items.length;
            o.counter = a > 1 ? W(i.tCounter, r.index, a) : "";
          }),
          y("BuildControls" + o, function () {
            if (t.items.length > 1 && i.arrows && !t.arrowLeft) {
              var n = i.arrowMarkup,
                o = (t.arrowLeft = e(
                  n.replace(/%title%/gi, i.tPrev).replace(/%dir%/gi, "left")
                ).addClass(g)),
                r = (t.arrowRight = e(
                  n.replace(/%title%/gi, i.tNext).replace(/%dir%/gi, "right")
                ).addClass(g));
              o.click(function () {
                t.prev();
              }),
                r.click(function () {
                  t.next();
                }),
                t.container.append(o.add(r));
            }
          }),
          y(d + o, function () {
            t._preloadTimeout && clearTimeout(t._preloadTimeout),
              (t._preloadTimeout = setTimeout(function () {
                t.preloadNearbyImages(), (t._preloadTimeout = null);
              }, 16));
          }),
          y(s + o, function () {
            n.off(o),
              t.wrap.off("click" + o),
              (t.arrowRight = t.arrowLeft = null);
          });
      },
      next: function () {
        (t.direction = !0), (t.index = j(t.index + 1)), t.updateItemHTML();
      },
      prev: function () {
        (t.direction = !1), (t.index = j(t.index - 1)), t.updateItemHTML();
      },
      goTo: function (e) {
        (t.direction = e >= t.index), (t.index = e), t.updateItemHTML();
      },
      preloadNearbyImages: function () {
        var e,
          i = t.st.gallery.preload,
          n = Math.min(i[0], t.items.length),
          o = Math.min(i[1], t.items.length);
        for (e = 1; e <= (t.direction ? o : n); e++)
          t._preloadItem(t.index + e);
        for (e = 1; e <= (t.direction ? n : o); e++)
          t._preloadItem(t.index - e);
      },
      _preloadItem: function (i) {
        if (((i = j(i)), !t.items[i].preloaded)) {
          var n = t.items[i];
          n.parsed || (n = t.parseEl(i)),
            b("LazyLoad", n),
            "image" === n.type &&
              (n.img = e('<img class="mfp-img" />')
                .on("load.mfploader", function () {
                  n.hasSize = !0;
                })
                .on("error.mfploader", function () {
                  (n.hasSize = !0), (n.loadError = !0), b("LazyLoadError", n);
                })
                .attr("src", n.src)),
            (n.preloaded = !0);
        }
      },
    },
  });
  var N = "retina";
  e.magnificPopup.registerModule(N, {
    options: {
      replaceSrc: function (e) {
        return e.src.replace(/\.\w+$/, function (e) {
          return "@2x" + e;
        });
      },
      ratio: 1,
    },
    proto: {
      initRetina: function () {
        if (window.devicePixelRatio > 1) {
          var e = t.st.retina,
            i = e.ratio;
          (i = isNaN(i) ? i() : i) > 1 &&
            (y("ImageHasSize." + N, function (e, t) {
              t.img.css({
                "max-width": t.img[0].naturalWidth / i,
                width: "100%",
              });
            }),
            y("ElementParse." + N, function (t, n) {
              n.src = e.replaceSrc(n, i);
            }));
        }
      },
    },
  }),
    x();
});
