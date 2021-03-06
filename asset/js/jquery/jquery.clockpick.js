/*
ClockPick, by Josh Nathanson
Version 1.2.4
Timepicker plugin for jQuery
See copyright at end of file
Complete documentation at http://www.oakcitygraphics.com/jquery/clockpick/trunk/ClockPick.cfm

name	 clockpick
type	 jQuery
param	 options                  hash                    object containing config options
param	 options[starthour]       int                     starting hour (use military int)
param	 options[endhour]         int                     ending hour (use military int)
param	 options[showminutes]     bool                    show minutes
param 	 options[minutedivisions] int                     number of divisions, i.e. 4 = :00, :15, :30, :45
param 	 options[military]        bool                    use 24hr time if true
param	 options[event]           string                  mouse event to trigger plugin
param	 options[layout]          string                  set div layout to vertical or horizontal
                                  ('vertical','horizontal')
param	 options[valuefield]      string                  field to insert time value, if not same as click field
                                  (name of input field)
param	 options[useBgiframe]	  bool					  set true if using bgIframe plugin
param	 options[hoursopacity]	  float					  set opacity of hours container
param 	 options[minutesopacity]  float					  set opacity of minutes container
param	 callback                 function                callback function - gets passed back the time value as a 
														  string
*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('q.2a.2S=7(j,k){u o={1I:8,1J:18,1p:2T,T:4,N:x,2b:\'1K\',2c:\'2d\',17:1L,19:x,2e:1,2f:1};3(j){q.2U(o,j)};u k=k||7(){},v=(o.2c==\'2d\');2g();q(1q)[o.2b](7(e){u g=1q,$U=q(1q),$E=q("E");3(!o.17){$U.1r("V").2h("V",1a)}9{q("[2i="+o.17+"]").1r("V").2h("V",1a)[0].2j()}q("#1M,#1s").1N();$p=q("<A z=\'1M\' D=\'1t\' />").1u($E);!o.19?$p.B("2k",o.2e):1L;1b($p);$O=q("<A D=\'2l\' z=\'O\' />").1u($E);$P=q("<A D=\'2l\' z=\'P\' />").1u($E);3(o.1p){$y=q("<A z=\'1s\' D=\'1t\' />").1u($E);!o.19?$y.B("2k",o.2f):1L;1b($y)}3(!v){$p.B("I","2m");$y.B("I","2m")}9{$O.1v(\'2n\');$P.1v(\'2n\')}2o();2p();7 2o(){u c=1;2q(h=o.1I;h<=o.1J;h++){3(h==12){c=1}W=((!o.N&&h>12)?h-12:h)+1c(h);3(!o.N&&h==0){W=\'12\'+1c(h)}$X=q("<A D=\'Y\' z=\'2r"+h+"F"+c+"\'>"+W+"</A>");3(o.N){$X.I(20)}1b($X);3(!v){$X.B("2s","C")}(h<12)?$O.Q($X):$P.Q($X);c++}$p.Q($O);!v?$p.Q("<A 2t=\'2u:C\' />"):\'\';$p.Q($P)}7 2v(h){1O=h;W=(!o.N&&h>12)?h-12:h;3(!o.N&&h==0){W=\'12\'}$y.2V();u n=1w/o.T,1x=1c(1O),1P=1;2q(m=0;m<1w;m=m+n){$1y=q("<A D=\'1Q\' z=\'"+1O+"F"+m+"\'>"+W+":"+((m<10)?"0":"")+m+1x+"</A>");3(!v){$1y.B("2s","C");3(o.T>6&&1P==o.T/2+1){$y.Q("<A 2t=\'2u:C\' />")}}$y.Q($1y);1b($1y);1P++}}7 1c(a){3(!o.N){w(a>=12)?\' 2W\':\' 2X\'}9{w\'\'}}7 2p(){3(!q.J.K&&e.1R!=\'2j\'){$p.B("C",e.2w-5).B("L",e.2x-(2Y.2Z($p.M()/2)));1S($p)}9 $U.30($p);$p.2y();3(o.19)1T($p)}7 1S(a){u b=G.Z.1U?G.Z.1U:G.E.1U;u c=G.Z.1V?G.Z.1V:G.E.1V;3(!q.J.K){u t=2z(a.B("L"));u l=2z(a.B("C"))}9{u t=a[0].1W;u l=a[0].1X}u d=G.Z.1Y?G.Z.1Y:G.E.1Y;3(t<=d&&!a.1z("#1s")){a.B("L",d+10+\'1A\')}9 3(t+a.M()-d>b){a.B("L",d+b-a.M()-10+\'1A\')}3(l<=0){a.B("C",\'31\')}}7 1T(a){3(32 q.2a.1Z==\'7\')a.1Z();9 21(\'1Z 33 34 35.\')}7 1b(a){3(a.R("z")==\'1M\'){a.1B(7(e){2A(e)})}9 3(a.R("z")==\'1s\'){a.1B(7(e){2B(e)})}9 3(a.R("D")==\'Y\'){a.1C(7(e){1D(a,e)});a.1B(7(){1E(a)});a.1K(7(){2C(a)})}9 3(a.R("D")==\'1Q\'){a.1C(7(){22(a)});a.1B(7(){23(a)});a.1K(7(){2D(a)})}};7 2A(e){2E{t=(e.1F)?e.1F:e.2F;3(!(q(t).1z("A[@D^=1t], 2G"))){3(!q.J.K){S()}}}2H(e){S()}}7 2B(e){2E{t=(e.1F)?e.1F:e.2F;3(!(q(t).1z("A[@D^=1t], 2G"))){3(!q.J.K){S()}}}2H(e){S()}}7 1D(a,e){u h=a.R("z").11(\'F\')[1],i=a.R("z").11(\'F\')[2],l,t;a.1v("14");3(o.1p){$y.36();2v(h);3(v){t=e.1R==\'1C\'?e.2x-15:$p.16().L+2+(a.M()*i);3(h<12){3(!q.J.K){l=$p.16().C-$y.I()}9{l=$p[0].1X-$y.I()}}9{3(!q.J.K){l=$p.16().C+$p.I()}9{l=$p[0].1X+$p.I()}}}9{l=(e.1R==\'1C\')?e.2w-10:$p.16().C+(a.I()-5)*i;3(h<12){3(!q.J.K){t=$p.16().L-$y.M()}9{t=$p[0].1W-$y.M()}}9{3(!q.J.K){t=$p.16().L+$p.M()}9{t=$p[0].1W+$p.M()}}}$y.B("C",l+\'1A\').B("L",t+\'1A\');1S($y);$y.2y();3(o.19)1T($y)}w x}7 1E(a){a.2I("14");w x}7 2C(a){h=a.R("z").11(\'F\')[1];1x=1c(h);1d=a.1e();3(1d.2J(\' \')!=-1){24=1d.3a(0,1d.2J(\' \'))}9{24=1d}a.1e(24+\':3b\'+1x);25(a);S()}7 22(a){a.1v("14");w x}7 23(a){a.2I("14");w x}7 2D(a){25(a);S()}7 25(a){3(!o.17){g.3c=a.1e()}9{q("3d[@2i="+o.17+"]").3e(a.1e())}k.3f($U,[a.1e()]);$U.1r("V",1a)}7 S(){3(o.1p){$y.1N()}$p.1N();$U.1r("V",1a)}7 1a(e){u d=$("A.14").1G()?$("A.14"):$("A.Y:2K"),H=d.1z(".Y")?\'1H\':\'26\',2L=(H==\'1H\')?d[0].z.11(\'F\')[2]:0,h=(H==\'26\')?d[0].z.11(\'F\')[0]:d[0].z.11(\'F\')[1];3(H==\'26\'){u f=h<12?\'1f\':\'1g\'}9{u f=h<12?\'1h\':\'1i\'}7 27(a){3(a.2M().1G()){1j(H+\'2N($1k)\');1j(H+\'2O($1k.2M(), e)\')}9{w x}}7 28(a){3(a.2P().1G()){1j(H+\'2N($1k)\');1j(H+\'2O($1k.2P(), e)\')}9{w x}}7 1l(a){u b=h>=12?\'#O\':\'#P\';$29=q(".Y[@z$=F"+2L+"]",b);3($29.1G()){1E(a);1D($29,e)}9{w x}}7 1m(a){1E(a);22($(".1Q:2K"))}7 1n(a){23(a);u b=h>=12?\'#P\':\'#O\';u c=q(".Y[@z^=2r"+h+"]",b);1D(c,e)}1o(e.3g){r 37:3(v){1o(f){r\'1f\':w x;s;r\'1g\':1n(d);s;r\'1h\':1m(d);s;r\'1i\':1l(d);s}}9{27(d)}s;r 38:3(v){27(d)}9{1o(f){r\'1f\':w x;s;r\'1g\':1n(d);s;r\'1h\':1m(d);s;r\'1i\':1l(d);s}}s;r 39:3(v){1o(f){r\'1f\':1n(d);s;r\'1g\':w x;s;r\'1h\':1l(d);s;r\'1i\':1m(d);s}}9{28(d)}s;r 3h:3(v){28(d)}9{1o(f){r\'1f\':1n(d);s;r\'1g\':w x;s;r\'1h\':1l(d);s;r\'1i\':1m(d);s}}s;r 13:1j(H+\'3i($1k)\');s}w x}w x});7 2g(){3(o.1I>=o.1J){21(\'2Q - 3j 1H 2R 3k 3l 3m 3n 1H.\');w x}9 3(1w%o.T!=0){21(\'2Q - 3o T 2R 3p 3q 3r 1w.\');w x}}w 1q}',62,214,'|||if||||function||else||||||||||||||||hourcont|jQuery|case|break||var||return|false|mc|id|div|css|left|class|body|_|document|divtype|width|browser|safari|top|height|military|hourcol1|hourcol2|append|attr|cleardivs|minutedivisions|self|keydown|displayhours|hd|CP_hour|documentElement||split|||CP_over||offset|valuefield||useBgiframe|keyhandler|binder|set_tt|str|text|m1|m2|h1|h2|eval|obj|hourtohour|hourtominute|minutetohour|switch|showminutes|this|unbind|CP_minutecont|CP|appendTo|addClass|60|tt|md|is|px|mouseout|mouseover|hourdiv_over|hourdiv_out|toElement|size|hour|starthour|endhour|click|null|CP_hourcont|remove|realhours|counter|CP_minute|type|rectify|bgi|clientHeight|clientWidth|offsetTop|offsetLeft|scrollTop|bgIframe||alert|minutediv_over|minutediv_out|cleanstr|setval|minute|divprev|divnext|newobj|fn|event|layout|vertical|hoursopacity|minutesopacity|errorcheck|bind|name|focus|opacity|CP_hourcol|auto|floatleft|renderhours|putcontainer|for|hr_|float|style|clear|renderminutes|pageX|pageY|show|parseInt|hourcont_out|minutecont_out|hourdiv_click|minutediv_click|try|relatedTarget|iframe|catch|removeClass|indexOf|first|hi|prev|div_out|div_over|next|Error|must|clockpick|true|extend|empty|PM|AM|Math|floor|after|10px|typeof|plugin|not|loaded|hide||||substring|00|value|input|val|apply|keyCode|40|div_click|start|be|less|than|end|param|divide|evenly|into'.split('|'),0,{}))
/*
+-----------------------------------------------------------------------+
| Copyright (c) 2007 Josh Nathanson                  |
| All rights reserved.                                                  |
|                                                                       |
| Redistribution and use in source and binary forms, with or without    |
| modification, are permitted provided that the following conditions    |
| are met:                                                              |
|                                                                       |
| o Redistributions of source code must retain the above copyright      |
|   notice, this list of conditions and the following disclaimer.       |
| o Redistributions in binary form must reproduce the above copyright   |
|   notice, this list of conditions and the following disclaimer in the |
|   documentation and/or other materials provided with the distribution.|
|                                                                       |
| THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
| "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
| LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
| A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
| OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
| SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
| LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
| DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
| THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
| (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
| OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
|                                                                       |
+-----------------------------------------------------------------------+
*/