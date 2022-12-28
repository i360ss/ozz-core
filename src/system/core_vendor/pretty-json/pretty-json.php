<?php
/**
 * JSON Formatter script and style
 * Source: https://www.cssscript.com/minimal-json-data-formatter-jsonviewer
 */

$pretty_json_script = 'var JSONViewer=function(c){var b={}.toString,d=b.call(new Date);function a(){this._dom_container=c.createElement("pre"),this._dom_container.classList.add("json-viewer")}function e(i,j,r,s,k){var p=b.call(j)===d,l=!p&&"object"==typeof j&&null!==j&&"toJSON"in j?j.toJSON():j;if("object"!=typeof l||null===l||p)i.appendChild(f(j,p));else{var t=r>=0&&k>=r,u=s>=0&&k>=s,o=Array.isArray(l),a=o?l:Object.keys(l);if(0===k){var v=g(a.length),m=h(o?"[":"{");a.length?(m.addEventListener("click",function(){t||(m.classList.toggle("collapsed"),v.classList.toggle("hide"),i.querySelector("ul").classList.toggle("hide"))}),u&&(m.classList.add("collapsed"),v.classList.remove("hide"))):m.classList.add("empty"),m.appendChild(v),i.appendChild(m)}if(a.length&&!t){var w=a.length-1,q=c.createElement("ul");q.setAttribute("data-level",k),q.classList.add("type-"+(o?"array":"object")),a.forEach(function(d,t){var b=o?d:j[d],a=c.createElement("li");if("object"==typeof b){if(!b||b instanceof Date)a.appendChild(c.createTextNode(o?"":d+": ")),a.appendChild(f(b||null,!0));else{var i=Array.isArray(b),m=i?b.length:Object.keys(b).length;if(m){var n=("string"==typeof d?d+": ":"")+(i?"[":"{"),l=h(n),u=g(m);r>=0&&k+1>=r?a.appendChild(c.createTextNode(n)):(l.appendChild(u),a.appendChild(l)),e(a,b,r,s,k+1),a.appendChild(c.createTextNode(i?"]":"}"));var v=a.querySelector("ul"),p=function(){l.classList.toggle("collapsed"),u.classList.toggle("hide"),v.classList.toggle("hide")};l.addEventListener("click",p),s>=0&&k+1>=s&&p()}else a.appendChild(c.createTextNode(d+": "+(i?"[]":"{}")))}}else o||a.appendChild(c.createTextNode(d+": ")),e(a,b,r,s,k+1);t<w&&a.appendChild(c.createTextNode(",")),q.appendChild(a)},this),i.appendChild(q)}else if(a.length&&t){var n=g(a.length);n.classList.remove("hide"),i.appendChild(n)}if(0===k){if(!a.length){var n=g(0);n.classList.remove("hide"),i.appendChild(n)}i.appendChild(c.createTextNode(o?"]":"}")),u&&i.querySelector("ul").classList.add("hide")}}}function f(a,f){var d=c.createElement("span"),b=typeof a,e=""+a;return"string"===b?e=`"`+a+`"`:null===a?b="null":f&&(b="date",e=a.toLocaleString()),d.className="type-"+b,d.textContent=e,d}function g(b){var a=c.createElement("span");return a.className="items-ph hide",a.innerHTML=i(b),a}function h(b){var a=c.createElement("a");return a.classList.add("list-link"),a.href="javascript:void(0)",a.innerHTML=b||"",a}function i(a){return a+" "+(a>1||0===a?"items":"item")}return a.prototype.showJSON=function(c,a,b){this._dom_container.innerHTML="",e(this._dom_container,c,"number"==typeof a?a:-1,"number"==typeof b?b:-1,0)},a.prototype.getContainer=function(){return this._dom_container},a}(document)';

$pretty_json_style = '.json-viewer{color:#000;padding-left:20px}.json-viewer ul{list-style-type:none;margin:0 0 0 1px;border-left:1px dotted #ccc;padding-left:2em}.json-viewer .hide{display:none}.json-viewer .type-string{color:#0b7500}.json-viewer .type-date{color:#cb7500}.json-viewer .type-boolean{color:#1a01cc;font-weight:700}.json-viewer .type-number{color:#1a01cc}.json-viewer .type-null,.json-viewer .type-undefined{color:#90a}.json-viewer a.list-link{color:#000;text-decoration:none;position:relative}.json-viewer a.list-link:before{color:#aaa;content:"\25BC";position:absolute;display:inline-block;width:1em;left:-1em}.json-viewer a.list-link.collapsed:before{content:"\25B6"}.json-viewer a.list-link.empty:before{content:""}.json-viewer .items-ph{color:#aaa;padding:0 1em}.json-viewer .items-ph:hover{text-decoration:underline}';