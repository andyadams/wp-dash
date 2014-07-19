/**
 * @license
 * Fuse - Lightweight fuzzy-search
 *
 * Copyright (c) 2012 Kirollos Risk <kirollos@gmail.com>.
 * All Rights Reserved. Apache Software License 2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
!function(t){function e(t,n){this.list=t,this.options=n=n||{};var i,o,s;for(i=0,keys=["sort","includeScore","shouldSort"],o=keys.length;o>i;i++)s=keys[i],this.options[s]=s in n?n[s]:e.defaultOptions[s];for(i=0,keys=["searchFn","sortFn","keys","getFn"],o=keys.length;o>i;i++)s=keys[i],this.options[s]=n[s]||e.defaultOptions[s]}var n=function(t,e){if(e=e||{},this.options=e,this.options.location=e.location||n.defaultOptions.location,this.options.distance="distance"in e?e.distance:n.defaultOptions.distance,this.options.threshold="threshold"in e?e.threshold:n.defaultOptions.threshold,this.options.maxPatternLength=e.maxPatternLength||n.defaultOptions.maxPatternLength,this.pattern=e.caseSensitive?t:t.toLowerCase(),this.patternLen=t.length,this.patternLen>this.options.maxPatternLength)throw new Error("Pattern length is too long");this.matchmask=1<<this.patternLen-1,this.patternAlphabet=this._calculatePatternAlphabet()};n.defaultOptions={location:0,distance:100,threshold:.6,maxPatternLength:32},n.prototype._calculatePatternAlphabet=function(){var t={},e=0;for(e=0;e<this.patternLen;e++)t[this.pattern.charAt(e)]=0;for(e=0;e<this.patternLen;e++)t[this.pattern.charAt(e)]|=1<<this.pattern.length-e-1;return t},n.prototype._bitapScore=function(t,e){var n=t/this.patternLen,i=Math.abs(this.options.location-e);return this.options.distance?n+i/this.options.distance:i?1:n},n.prototype.search=function(t){if(t=this.options.caseSensitive?t:t.toLowerCase(),this.pattern===t)return{isMatch:!0,score:0};var e,n,i,o,s,r,a,h,p,c=t.length,l=this.options.location,u=this.options.threshold,f=t.indexOf(this.pattern,l),d=this.patternLen+c,g=1,m=[];for(-1!=f&&(u=Math.min(this._bitapScore(0,f),u),f=t.lastIndexOf(this.pattern,l+this.patternLen),-1!=f&&(u=Math.min(this._bitapScore(0,f),u))),f=-1,e=0;e<this.patternLen;e++){for(i=0,o=d;o>i;)this._bitapScore(e,l+o)<=u?i=o:d=o,o=Math.floor((d-i)/2+i);for(d=o,s=Math.max(1,l-o+1),r=Math.min(l+o,c)+this.patternLen,a=Array(r+2),a[r+1]=(1<<e)-1,n=r;n>=s;n--)if(p=this.patternAlphabet[t.charAt(n-1)],a[n]=0===e?(a[n+1]<<1|1)&p:(a[n+1]<<1|1)&p|((h[n+1]|h[n])<<1|1)|h[n+1],a[n]&this.matchmask&&(g=this._bitapScore(e,n-1),u>=g)){if(u=g,f=n-1,m.push(f),!(f>l))break;s=Math.max(1,2*l-f)}if(this._bitapScore(e+1,l)>u)break;h=a}return{isMatch:f>=0,score:g}};var i={deepValue:function(t,e){for(var n=0,e=e.split("."),i=e.length;i>n;n++){if(!t)return null;t=t[e[n]]}return t}};e.defaultOptions={id:null,caseSensitive:!1,includeScore:!1,shouldSort:!0,searchFn:n,sortFn:function(t,e){return t.score-e.score},getFn:i.deepValue,keys:[]},e.prototype.search=function(t){var e,n,o,s,r,a=new this.options.searchFn(t,this.options),h=this.list,p=h.length,c=this.options,l=this.options.keys,u=l.length,f=[],d={},g=[],m=function(t,e,n){void 0!==t&&null!==t&&"string"==typeof t&&(s=a.search(t),s.isMatch&&(r=d[n],r?r.score=Math.min(r.score,s.score):(d[n]={item:e,score:s.score},f.push(d[n]))))};if("string"==typeof h[0])for(var e=0;p>e;e++)m(h[e],e,e);else for(var e=0;p>e;e++)for(o=h[e],n=0;u>n;n++)m(this.options.getFn(o,l[n]),o,e);c.shouldSort&&f.sort(c.sortFn);for(var y=c.includeScore?function(t){return f[t]}:function(t){return f[t].item},L=c.id?function(t){return i.deepValue(y(t),c.id)}:function(t){return y(t)},e=0,v=f.length;v>e;e++)g.push(L(e));return g},"object"==typeof exports?module.exports=e:"function"==typeof define&&define.amd?define(function(){return e}):t.Fuse=e}(this);