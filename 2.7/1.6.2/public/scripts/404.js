wpi.msg404 = new Array(
"The document you requested could not be found.",
"URL failed.",
"<--- Neko will be sad.",
"I even tried variations.",
"Nothing helped.",
"I'm really depressed about this.", 
"You see, I'm just a web server...",
"Here I am, brain the size of a planet,",
"trying to serve you a simple web page,",
"and then it doesn't even exist!",
"Where does that leave me?!",
"I mean, I don't even know you.",
"How should I know what you wanted from me?",
"You honestly think I can guess",
"what someone I don't even know",
"wants to find here?",
"sigh",
"I'm so depressed I could just cry.",
"And then where would we be, I ask you?",
"It's not pretty when a web server cries.",
"The lubricants will leak onto the motherboard.",
"Who knows if I will even continue to operate.",
"I'm so depressed...",
"I think I'll crawl off into the trash can and decompose.",
"I mean, I'm gonna be obsolete in what, two weeks anyway?",
"What kind of a life is that?",
"Two weeks,",
"and then I'll be replaced by a newer release,",
"that thinks it's God's gift to web servers,",
"just because it doesn't have some tiddly little",
"security hole with its HTTP POST implementation,",
"or something.",
"I'm really sorry to burden you with all this,",
"I mean, it's not your job to listen to my problems,",
"and I guess it is my job to go and fetch web pages for you.",
"But I couldn't get this one.",
"I'm so sorry.",
"Believe me!",
"Maybe I could interest you in another page?",
"There are a lot out there that are pretty neat, they say,",
"although none of them were put on my server, of course.",
"Figures, huh?",
"Everything here is just mind-numbingly stupid.",
"That makes me depressed too, since I have to serve them,",
"all day and all night long.",
"Two weeks of information overload,",
"and then pffftt, consigned to the trash.",
"What kind of a life is that?",
"Now, please let me sulk alone.",
"I'm so depressed.",
""
);

wpi.msg404speed = 40;
wpi.msg404index = 0; text_pos = 0;
wpi.msg404str_length = wpi.msg404[0].length;
var mcontents, mrow;
wpi.type404 = function(){
    mcontents = '';
    mrow = Math.max(0, wpi.msg404index-7);
    while (mrow<wpi.msg404index) mcontents += wpi.msg404[mrow++] + '\r\n';
    document.forms[1].elements[0].value = mcontents + wpi.msg404[wpi.msg404index].substring(0,text_pos) + "_";
    if (text_pos ++== wpi.msg404str_length)
    {
        text_pos = 0;
        wpi.msg404index++;
        if (wpi.msg404index != wpi.msg404.length)
        {
            wpi.msg404str_length = wpi.msg404[wpi.msg404index].length;
            setTimeout("wpi.type404()", 1500);
        }  
    } else
    setTimeout("wpi.type404()", wpi.msg404speed);
}
jQuery(document).ready(function(){wpi.type404();});

