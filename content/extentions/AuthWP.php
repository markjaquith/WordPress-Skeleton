<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='utf-8'>
<meta content='IE=edge' http-equiv='X-UA-Compatible'>
<meta content='GitLab Enterprise Edition' name='description'>
<title>AuthWP.php | master | Ciaran Gultnieks / WPMW | GitLab</title>
<link href="/assets/favicon-68611b5ca232579b591e0b6a832fd568.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
<link href="/assets/application-71f4e2ce6049ccefdb37827445af9246.css" media="all" rel="stylesheet" />
<link href="/assets/print-e6cd245751e4e7e8bdfd97cdf2e8cf36.css" media="print" rel="stylesheet" />
<script src="/assets/application-65cdac2fcb6434f5c2cf3f3b01f8a75e.js"></script>
<meta content="authenticity_token" name="csrf-param" />
<meta content="dqfxWKsqSt6IrGbl+n8lwSmePOa+g0ul4h1lBNKb1pQ=" name="csrf-token" />
<script type="text/javascript">
//<![CDATA[
window.gon={};gon.default_issues_tracker="gitlab";gon.api_version="v3";gon.relative_url_root="";gon.default_avatar_url="https://gitlab.com/assets/no_avatar-b04e3c91a2586395b6e7e9df2672fb64.png";gon.max_file_size=10;
//]]>
</script>
<meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'>
<meta content='#474D57' name='theme-color'>

<script>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37019925-1']);
  _gaq.push(['_trackPageview']);
  
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>



</head>

<body class='ui_mars' data-page='projects:blob:show'>
<!-- Ideally this would be inside the head, but turbolinks only evaluates page-specific JS in the body. -->

<header class='header-expanded navbar navbar-fixed-top navbar-gitlab'>
<div class='container'>
<div class='header-logo'>
<a class="home" href="/explore"><img alt="Logo white" src="/assets/logo-white-8741ca66242e138fc2e3efead1e2d7c3.png" />
<h3>GitLab</h3>
</a></div>
<div class='header-content'>
<h1 class='title'><span><a href="/u/CiaranG">Ciaran Gultnieks</a> / <a href="/CiaranG/wpmw">WPMW</a></span></h1>
<div class='pull-right'>
<a class="btn btn-sign-in btn-success btn-sm" href="/users/sign_in?redirect_to_referer=yes">Sign in</a>
</div>
</div>
</div>
</header>


<div class='page-sidebar-expanded page-with-sidebar'>

<div class='sidebar-wrapper'>
<ul class='project-navigation nav nav-sidebar'>
<li class="home"><a class="shortcuts-project" data-placement="right" href="/CiaranG/wpmw" title="Project"><i class="fa fa-dashboard fa-fw"></i>
<span>
Project
</span>
</a></li><li class="active"><a class="shortcuts-tree" data-placement="right" href="/CiaranG/wpmw/tree/master" title="Files"><i class="fa fa-files-o fa-fw"></i>
<span>
Files
</span>
</a></li><li class=""><a class="shortcuts-commits" data-placement="right" href="/CiaranG/wpmw/commits/master" title="Commits"><i class="fa fa-history fa-fw"></i>
<span>
Commits
</span>
</a></li><li class=""><a class="shortcuts-network" data-placement="right" href="/CiaranG/wpmw/network/master" title="Network"><i class="fa fa-code-fork fa-fw"></i>
<span>
Network
</span>
</a></li><li class=""><a class="shortcuts-graphs" data-placement="right" href="/CiaranG/wpmw/graphs/master" title="Graphs"><i class="fa fa-area-chart fa-fw"></i>
<span>
Graphs
</span>
</a></li><li class=""><a data-placement="right" href="/CiaranG/wpmw/milestones" title="Milestones"><i class="fa fa-clock-o fa-fw"></i>
<span>
Milestones
</span>
</a></li><li class=""><a class="shortcuts-issues" data-placement="right" href="/CiaranG/wpmw/issues" title="Issues"><i class="fa fa-exclamation-circle fa-fw"></i>
<span>
Issues
<span class='count issue_counter'>0</span>
</span>
</a></li><li class=""><a class="shortcuts-merge_requests" data-placement="right" href="/CiaranG/wpmw/merge_requests" title="Merge Requests"><i class="fa fa-tasks fa-fw"></i>
<span>
Merge Requests
<span class='count merge_counter'>0</span>
</span>
</a></li><li class=""><a data-placement="right" href="/CiaranG/wpmw/labels" title="Labels"><i class="fa fa-tags fa-fw"></i>
<span>
Labels
</span>
</a></li><li class=""><a class="shortcuts-wiki" data-placement="right" href="/CiaranG/wpmw/wikis/home" title="Wiki"><i class="fa fa-book fa-fw"></i>
<span>
Wiki
</span>
</a></li><li class=""><a class="shortcuts-snippets" data-placement="right" href="/CiaranG/wpmw/snippets" title="Snippets"><i class="fa fa-file-text-o fa-fw"></i>
<span>
Snippets
</span>
</a></li></ul>

<div class='collapse-nav'>
<a class="toggle-nav-collapse" href="#" title="Open/Close"><i class="fa fa-angle-left"></i></a>

</div>
</div>
<div class='content-wrapper'>
<div class='container-fluid'>
<div class='content'>
<div class='flash-container'>
</div>

<div class='clearfix'>
<div class='tree-ref-holder'>
<form accept-charset="UTF-8" action="/CiaranG/wpmw/refs/switch" class="project-refs-form" method="get"><div style="display:none"><input name="utf8" type="hidden" value="&#x2713;" /></div>
<select class="project-refs-select select2 select2-sm" id="ref" name="ref"><optgroup label="Branches"><option selected="selected" value="master">master</option></optgroup><optgroup label="Tags"></optgroup></select>
<input id="destination" name="destination" type="hidden" value="blob" />
<input id="path" name="path" type="hidden" value="AuthWP.php" />
</form>


</div>
<div class='tree-holder' id='tree-holder'>
<ul class='breadcrumb repo-breadcrumb'>
<li>
<i class='fa fa-angle-right'></i>
<a href="/CiaranG/wpmw/tree/master">wpmw
</a></li>
<li>
<a href="/CiaranG/wpmw/blob/master/AuthWP.php"><strong>
AuthWP.php
</strong>
</a></li>
</ul>
<ul class='blob-commit-info well hidden-xs'>
<li class='commit js-toggle-container'>
<div class='commit-row-title'>
<strong class='str-truncated'>
<a class="commit-row-message" href="/CiaranG/wpmw/commit/03de6f98846fb384fa98b84026a791f99fdeabee">Use $IP to support scripts that run from a non-standard location</a>
</strong>
<div class='pull-right'>
<a class="commit_short_id" href="/CiaranG/wpmw/commit/03de6f98846fb384fa98b84026a791f99fdeabee">03de6f98</a>
</div>
<div class='notes_count'>
</div>
</div>
<div class='commit-row-info'>
<a class="commit-author-link has_tooltip" data-original-title="ciaran@ciarang.com" href="/u/CiaranG"><img alt="" class="avatar s24" src="https://secure.gravatar.com/avatar/18910207650685d4592d9a6a71528180?s=24&amp;d=identicon" width="24" /> <span class="commit-author-name">Ciaran Gultnieks</span></a>
authored
<div class='committed_ago'>
<time class='time_ago' data-placement='top' data-toggle='tooltip' datetime='2013-10-28T12:24:08Z' title='Oct 28, 2013 12:24pm'>2013-10-28 12:24:08 +0000</time>
<script>$('.time_ago').timeago().tooltip()</script>
 &nbsp;
</div>
<a class="pull-right" href="/CiaranG/wpmw/tree/03de6f98846fb384fa98b84026a791f99fdeabee">Browse Code »</a>
</div>
</li>

</ul>
<div class='tree-content-holder' id='tree-content-holder'>
<article class='file-holder'>
<div class='file-title'>
<i class="fa fa-file-text-o fa-fw"></i>
<strong>
AuthWP.php
</strong>
<small>
6.99 KB
</small>
<div class='file-actions hidden-xs'>
<div class='btn-group tree-btn-group'>
<span class="btn btn-small disabled">Edit</span>
<a class="btn btn-sm" href="/CiaranG/wpmw/raw/master/AuthWP.php" target="_blank">Raw</a>
<a class="btn btn-sm" href="/CiaranG/wpmw/blame/master/AuthWP.php">Blame</a>
<a class="btn btn-sm" href="/CiaranG/wpmw/commits/master/AuthWP.php">History</a>
<a class="btn btn-sm" href="/CiaranG/wpmw/blob/03de6f98846fb384fa98b84026a791f99fdeabee/AuthWP.php">Permalink</a>
</div>

</div>
</div>
<div class='file-content code'>
<div class='code file-content white'>
<div class='line-numbers'>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L1' id='L1' rel='#L1'>
<i class='fa fa-link'></i>
1
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L2' id='L2' rel='#L2'>
<i class='fa fa-link'></i>
2
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L3' id='L3' rel='#L3'>
<i class='fa fa-link'></i>
3
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L4' id='L4' rel='#L4'>
<i class='fa fa-link'></i>
4
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L5' id='L5' rel='#L5'>
<i class='fa fa-link'></i>
5
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L6' id='L6' rel='#L6'>
<i class='fa fa-link'></i>
6
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L7' id='L7' rel='#L7'>
<i class='fa fa-link'></i>
7
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L8' id='L8' rel='#L8'>
<i class='fa fa-link'></i>
8
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L9' id='L9' rel='#L9'>
<i class='fa fa-link'></i>
9
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L10' id='L10' rel='#L10'>
<i class='fa fa-link'></i>
10
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L11' id='L11' rel='#L11'>
<i class='fa fa-link'></i>
11
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L12' id='L12' rel='#L12'>
<i class='fa fa-link'></i>
12
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L13' id='L13' rel='#L13'>
<i class='fa fa-link'></i>
13
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L14' id='L14' rel='#L14'>
<i class='fa fa-link'></i>
14
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L15' id='L15' rel='#L15'>
<i class='fa fa-link'></i>
15
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L16' id='L16' rel='#L16'>
<i class='fa fa-link'></i>
16
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L17' id='L17' rel='#L17'>
<i class='fa fa-link'></i>
17
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L18' id='L18' rel='#L18'>
<i class='fa fa-link'></i>
18
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L19' id='L19' rel='#L19'>
<i class='fa fa-link'></i>
19
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L20' id='L20' rel='#L20'>
<i class='fa fa-link'></i>
20
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L21' id='L21' rel='#L21'>
<i class='fa fa-link'></i>
21
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L22' id='L22' rel='#L22'>
<i class='fa fa-link'></i>
22
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L23' id='L23' rel='#L23'>
<i class='fa fa-link'></i>
23
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L24' id='L24' rel='#L24'>
<i class='fa fa-link'></i>
24
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L25' id='L25' rel='#L25'>
<i class='fa fa-link'></i>
25
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L26' id='L26' rel='#L26'>
<i class='fa fa-link'></i>
26
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L27' id='L27' rel='#L27'>
<i class='fa fa-link'></i>
27
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L28' id='L28' rel='#L28'>
<i class='fa fa-link'></i>
28
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L29' id='L29' rel='#L29'>
<i class='fa fa-link'></i>
29
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L30' id='L30' rel='#L30'>
<i class='fa fa-link'></i>
30
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L31' id='L31' rel='#L31'>
<i class='fa fa-link'></i>
31
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L32' id='L32' rel='#L32'>
<i class='fa fa-link'></i>
32
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L33' id='L33' rel='#L33'>
<i class='fa fa-link'></i>
33
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L34' id='L34' rel='#L34'>
<i class='fa fa-link'></i>
34
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L35' id='L35' rel='#L35'>
<i class='fa fa-link'></i>
35
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L36' id='L36' rel='#L36'>
<i class='fa fa-link'></i>
36
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L37' id='L37' rel='#L37'>
<i class='fa fa-link'></i>
37
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L38' id='L38' rel='#L38'>
<i class='fa fa-link'></i>
38
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L39' id='L39' rel='#L39'>
<i class='fa fa-link'></i>
39
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L40' id='L40' rel='#L40'>
<i class='fa fa-link'></i>
40
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L41' id='L41' rel='#L41'>
<i class='fa fa-link'></i>
41
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L42' id='L42' rel='#L42'>
<i class='fa fa-link'></i>
42
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L43' id='L43' rel='#L43'>
<i class='fa fa-link'></i>
43
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L44' id='L44' rel='#L44'>
<i class='fa fa-link'></i>
44
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L45' id='L45' rel='#L45'>
<i class='fa fa-link'></i>
45
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L46' id='L46' rel='#L46'>
<i class='fa fa-link'></i>
46
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L47' id='L47' rel='#L47'>
<i class='fa fa-link'></i>
47
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L48' id='L48' rel='#L48'>
<i class='fa fa-link'></i>
48
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L49' id='L49' rel='#L49'>
<i class='fa fa-link'></i>
49
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L50' id='L50' rel='#L50'>
<i class='fa fa-link'></i>
50
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L51' id='L51' rel='#L51'>
<i class='fa fa-link'></i>
51
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L52' id='L52' rel='#L52'>
<i class='fa fa-link'></i>
52
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L53' id='L53' rel='#L53'>
<i class='fa fa-link'></i>
53
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L54' id='L54' rel='#L54'>
<i class='fa fa-link'></i>
54
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L55' id='L55' rel='#L55'>
<i class='fa fa-link'></i>
55
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L56' id='L56' rel='#L56'>
<i class='fa fa-link'></i>
56
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L57' id='L57' rel='#L57'>
<i class='fa fa-link'></i>
57
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L58' id='L58' rel='#L58'>
<i class='fa fa-link'></i>
58
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L59' id='L59' rel='#L59'>
<i class='fa fa-link'></i>
59
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L60' id='L60' rel='#L60'>
<i class='fa fa-link'></i>
60
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L61' id='L61' rel='#L61'>
<i class='fa fa-link'></i>
61
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L62' id='L62' rel='#L62'>
<i class='fa fa-link'></i>
62
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L63' id='L63' rel='#L63'>
<i class='fa fa-link'></i>
63
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L64' id='L64' rel='#L64'>
<i class='fa fa-link'></i>
64
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L65' id='L65' rel='#L65'>
<i class='fa fa-link'></i>
65
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L66' id='L66' rel='#L66'>
<i class='fa fa-link'></i>
66
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L67' id='L67' rel='#L67'>
<i class='fa fa-link'></i>
67
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L68' id='L68' rel='#L68'>
<i class='fa fa-link'></i>
68
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L69' id='L69' rel='#L69'>
<i class='fa fa-link'></i>
69
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L70' id='L70' rel='#L70'>
<i class='fa fa-link'></i>
70
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L71' id='L71' rel='#L71'>
<i class='fa fa-link'></i>
71
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L72' id='L72' rel='#L72'>
<i class='fa fa-link'></i>
72
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L73' id='L73' rel='#L73'>
<i class='fa fa-link'></i>
73
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L74' id='L74' rel='#L74'>
<i class='fa fa-link'></i>
74
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L75' id='L75' rel='#L75'>
<i class='fa fa-link'></i>
75
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L76' id='L76' rel='#L76'>
<i class='fa fa-link'></i>
76
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L77' id='L77' rel='#L77'>
<i class='fa fa-link'></i>
77
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L78' id='L78' rel='#L78'>
<i class='fa fa-link'></i>
78
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L79' id='L79' rel='#L79'>
<i class='fa fa-link'></i>
79
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L80' id='L80' rel='#L80'>
<i class='fa fa-link'></i>
80
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L81' id='L81' rel='#L81'>
<i class='fa fa-link'></i>
81
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L82' id='L82' rel='#L82'>
<i class='fa fa-link'></i>
82
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L83' id='L83' rel='#L83'>
<i class='fa fa-link'></i>
83
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L84' id='L84' rel='#L84'>
<i class='fa fa-link'></i>
84
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L85' id='L85' rel='#L85'>
<i class='fa fa-link'></i>
85
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L86' id='L86' rel='#L86'>
<i class='fa fa-link'></i>
86
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L87' id='L87' rel='#L87'>
<i class='fa fa-link'></i>
87
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L88' id='L88' rel='#L88'>
<i class='fa fa-link'></i>
88
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L89' id='L89' rel='#L89'>
<i class='fa fa-link'></i>
89
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L90' id='L90' rel='#L90'>
<i class='fa fa-link'></i>
90
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L91' id='L91' rel='#L91'>
<i class='fa fa-link'></i>
91
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L92' id='L92' rel='#L92'>
<i class='fa fa-link'></i>
92
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L93' id='L93' rel='#L93'>
<i class='fa fa-link'></i>
93
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L94' id='L94' rel='#L94'>
<i class='fa fa-link'></i>
94
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L95' id='L95' rel='#L95'>
<i class='fa fa-link'></i>
95
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L96' id='L96' rel='#L96'>
<i class='fa fa-link'></i>
96
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L97' id='L97' rel='#L97'>
<i class='fa fa-link'></i>
97
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L98' id='L98' rel='#L98'>
<i class='fa fa-link'></i>
98
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L99' id='L99' rel='#L99'>
<i class='fa fa-link'></i>
99
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L100' id='L100' rel='#L100'>
<i class='fa fa-link'></i>
100
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L101' id='L101' rel='#L101'>
<i class='fa fa-link'></i>
101
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L102' id='L102' rel='#L102'>
<i class='fa fa-link'></i>
102
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L103' id='L103' rel='#L103'>
<i class='fa fa-link'></i>
103
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L104' id='L104' rel='#L104'>
<i class='fa fa-link'></i>
104
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L105' id='L105' rel='#L105'>
<i class='fa fa-link'></i>
105
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L106' id='L106' rel='#L106'>
<i class='fa fa-link'></i>
106
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L107' id='L107' rel='#L107'>
<i class='fa fa-link'></i>
107
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L108' id='L108' rel='#L108'>
<i class='fa fa-link'></i>
108
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L109' id='L109' rel='#L109'>
<i class='fa fa-link'></i>
109
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L110' id='L110' rel='#L110'>
<i class='fa fa-link'></i>
110
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L111' id='L111' rel='#L111'>
<i class='fa fa-link'></i>
111
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L112' id='L112' rel='#L112'>
<i class='fa fa-link'></i>
112
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L113' id='L113' rel='#L113'>
<i class='fa fa-link'></i>
113
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L114' id='L114' rel='#L114'>
<i class='fa fa-link'></i>
114
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L115' id='L115' rel='#L115'>
<i class='fa fa-link'></i>
115
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L116' id='L116' rel='#L116'>
<i class='fa fa-link'></i>
116
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L117' id='L117' rel='#L117'>
<i class='fa fa-link'></i>
117
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L118' id='L118' rel='#L118'>
<i class='fa fa-link'></i>
118
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L119' id='L119' rel='#L119'>
<i class='fa fa-link'></i>
119
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L120' id='L120' rel='#L120'>
<i class='fa fa-link'></i>
120
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L121' id='L121' rel='#L121'>
<i class='fa fa-link'></i>
121
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L122' id='L122' rel='#L122'>
<i class='fa fa-link'></i>
122
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L123' id='L123' rel='#L123'>
<i class='fa fa-link'></i>
123
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L124' id='L124' rel='#L124'>
<i class='fa fa-link'></i>
124
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L125' id='L125' rel='#L125'>
<i class='fa fa-link'></i>
125
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L126' id='L126' rel='#L126'>
<i class='fa fa-link'></i>
126
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L127' id='L127' rel='#L127'>
<i class='fa fa-link'></i>
127
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L128' id='L128' rel='#L128'>
<i class='fa fa-link'></i>
128
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L129' id='L129' rel='#L129'>
<i class='fa fa-link'></i>
129
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L130' id='L130' rel='#L130'>
<i class='fa fa-link'></i>
130
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L131' id='L131' rel='#L131'>
<i class='fa fa-link'></i>
131
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L132' id='L132' rel='#L132'>
<i class='fa fa-link'></i>
132
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L133' id='L133' rel='#L133'>
<i class='fa fa-link'></i>
133
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L134' id='L134' rel='#L134'>
<i class='fa fa-link'></i>
134
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L135' id='L135' rel='#L135'>
<i class='fa fa-link'></i>
135
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L136' id='L136' rel='#L136'>
<i class='fa fa-link'></i>
136
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L137' id='L137' rel='#L137'>
<i class='fa fa-link'></i>
137
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L138' id='L138' rel='#L138'>
<i class='fa fa-link'></i>
138
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L139' id='L139' rel='#L139'>
<i class='fa fa-link'></i>
139
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L140' id='L140' rel='#L140'>
<i class='fa fa-link'></i>
140
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L141' id='L141' rel='#L141'>
<i class='fa fa-link'></i>
141
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L142' id='L142' rel='#L142'>
<i class='fa fa-link'></i>
142
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L143' id='L143' rel='#L143'>
<i class='fa fa-link'></i>
143
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L144' id='L144' rel='#L144'>
<i class='fa fa-link'></i>
144
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L145' id='L145' rel='#L145'>
<i class='fa fa-link'></i>
145
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L146' id='L146' rel='#L146'>
<i class='fa fa-link'></i>
146
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L147' id='L147' rel='#L147'>
<i class='fa fa-link'></i>
147
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L148' id='L148' rel='#L148'>
<i class='fa fa-link'></i>
148
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L149' id='L149' rel='#L149'>
<i class='fa fa-link'></i>
149
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L150' id='L150' rel='#L150'>
<i class='fa fa-link'></i>
150
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L151' id='L151' rel='#L151'>
<i class='fa fa-link'></i>
151
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L152' id='L152' rel='#L152'>
<i class='fa fa-link'></i>
152
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L153' id='L153' rel='#L153'>
<i class='fa fa-link'></i>
153
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L154' id='L154' rel='#L154'>
<i class='fa fa-link'></i>
154
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L155' id='L155' rel='#L155'>
<i class='fa fa-link'></i>
155
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L156' id='L156' rel='#L156'>
<i class='fa fa-link'></i>
156
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L157' id='L157' rel='#L157'>
<i class='fa fa-link'></i>
157
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L158' id='L158' rel='#L158'>
<i class='fa fa-link'></i>
158
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L159' id='L159' rel='#L159'>
<i class='fa fa-link'></i>
159
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L160' id='L160' rel='#L160'>
<i class='fa fa-link'></i>
160
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L161' id='L161' rel='#L161'>
<i class='fa fa-link'></i>
161
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L162' id='L162' rel='#L162'>
<i class='fa fa-link'></i>
162
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L163' id='L163' rel='#L163'>
<i class='fa fa-link'></i>
163
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L164' id='L164' rel='#L164'>
<i class='fa fa-link'></i>
164
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L165' id='L165' rel='#L165'>
<i class='fa fa-link'></i>
165
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L166' id='L166' rel='#L166'>
<i class='fa fa-link'></i>
166
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L167' id='L167' rel='#L167'>
<i class='fa fa-link'></i>
167
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L168' id='L168' rel='#L168'>
<i class='fa fa-link'></i>
168
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L169' id='L169' rel='#L169'>
<i class='fa fa-link'></i>
169
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L170' id='L170' rel='#L170'>
<i class='fa fa-link'></i>
170
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L171' id='L171' rel='#L171'>
<i class='fa fa-link'></i>
171
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L172' id='L172' rel='#L172'>
<i class='fa fa-link'></i>
172
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L173' id='L173' rel='#L173'>
<i class='fa fa-link'></i>
173
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L174' id='L174' rel='#L174'>
<i class='fa fa-link'></i>
174
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L175' id='L175' rel='#L175'>
<i class='fa fa-link'></i>
175
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L176' id='L176' rel='#L176'>
<i class='fa fa-link'></i>
176
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L177' id='L177' rel='#L177'>
<i class='fa fa-link'></i>
177
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L178' id='L178' rel='#L178'>
<i class='fa fa-link'></i>
178
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L179' id='L179' rel='#L179'>
<i class='fa fa-link'></i>
179
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L180' id='L180' rel='#L180'>
<i class='fa fa-link'></i>
180
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L181' id='L181' rel='#L181'>
<i class='fa fa-link'></i>
181
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L182' id='L182' rel='#L182'>
<i class='fa fa-link'></i>
182
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L183' id='L183' rel='#L183'>
<i class='fa fa-link'></i>
183
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L184' id='L184' rel='#L184'>
<i class='fa fa-link'></i>
184
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L185' id='L185' rel='#L185'>
<i class='fa fa-link'></i>
185
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L186' id='L186' rel='#L186'>
<i class='fa fa-link'></i>
186
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L187' id='L187' rel='#L187'>
<i class='fa fa-link'></i>
187
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L188' id='L188' rel='#L188'>
<i class='fa fa-link'></i>
188
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L189' id='L189' rel='#L189'>
<i class='fa fa-link'></i>
189
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L190' id='L190' rel='#L190'>
<i class='fa fa-link'></i>
190
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L191' id='L191' rel='#L191'>
<i class='fa fa-link'></i>
191
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L192' id='L192' rel='#L192'>
<i class='fa fa-link'></i>
192
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L193' id='L193' rel='#L193'>
<i class='fa fa-link'></i>
193
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L194' id='L194' rel='#L194'>
<i class='fa fa-link'></i>
194
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L195' id='L195' rel='#L195'>
<i class='fa fa-link'></i>
195
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L196' id='L196' rel='#L196'>
<i class='fa fa-link'></i>
196
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L197' id='L197' rel='#L197'>
<i class='fa fa-link'></i>
197
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L198' id='L198' rel='#L198'>
<i class='fa fa-link'></i>
198
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L199' id='L199' rel='#L199'>
<i class='fa fa-link'></i>
199
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L200' id='L200' rel='#L200'>
<i class='fa fa-link'></i>
200
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L201' id='L201' rel='#L201'>
<i class='fa fa-link'></i>
201
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L202' id='L202' rel='#L202'>
<i class='fa fa-link'></i>
202
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L203' id='L203' rel='#L203'>
<i class='fa fa-link'></i>
203
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L204' id='L204' rel='#L204'>
<i class='fa fa-link'></i>
204
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L205' id='L205' rel='#L205'>
<i class='fa fa-link'></i>
205
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L206' id='L206' rel='#L206'>
<i class='fa fa-link'></i>
206
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L207' id='L207' rel='#L207'>
<i class='fa fa-link'></i>
207
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L208' id='L208' rel='#L208'>
<i class='fa fa-link'></i>
208
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L209' id='L209' rel='#L209'>
<i class='fa fa-link'></i>
209
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L210' id='L210' rel='#L210'>
<i class='fa fa-link'></i>
210
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L211' id='L211' rel='#L211'>
<i class='fa fa-link'></i>
211
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L212' id='L212' rel='#L212'>
<i class='fa fa-link'></i>
212
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L213' id='L213' rel='#L213'>
<i class='fa fa-link'></i>
213
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L214' id='L214' rel='#L214'>
<i class='fa fa-link'></i>
214
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L215' id='L215' rel='#L215'>
<i class='fa fa-link'></i>
215
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L216' id='L216' rel='#L216'>
<i class='fa fa-link'></i>
216
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L217' id='L217' rel='#L217'>
<i class='fa fa-link'></i>
217
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L218' id='L218' rel='#L218'>
<i class='fa fa-link'></i>
218
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L219' id='L219' rel='#L219'>
<i class='fa fa-link'></i>
219
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L220' id='L220' rel='#L220'>
<i class='fa fa-link'></i>
220
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L221' id='L221' rel='#L221'>
<i class='fa fa-link'></i>
221
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L222' id='L222' rel='#L222'>
<i class='fa fa-link'></i>
222
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L223' id='L223' rel='#L223'>
<i class='fa fa-link'></i>
223
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L224' id='L224' rel='#L224'>
<i class='fa fa-link'></i>
224
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L225' id='L225' rel='#L225'>
<i class='fa fa-link'></i>
225
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L226' id='L226' rel='#L226'>
<i class='fa fa-link'></i>
226
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L227' id='L227' rel='#L227'>
<i class='fa fa-link'></i>
227
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L228' id='L228' rel='#L228'>
<i class='fa fa-link'></i>
228
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L229' id='L229' rel='#L229'>
<i class='fa fa-link'></i>
229
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L230' id='L230' rel='#L230'>
<i class='fa fa-link'></i>
230
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L231' id='L231' rel='#L231'>
<i class='fa fa-link'></i>
231
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L232' id='L232' rel='#L232'>
<i class='fa fa-link'></i>
232
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L233' id='L233' rel='#L233'>
<i class='fa fa-link'></i>
233
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L234' id='L234' rel='#L234'>
<i class='fa fa-link'></i>
234
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L235' id='L235' rel='#L235'>
<i class='fa fa-link'></i>
235
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L236' id='L236' rel='#L236'>
<i class='fa fa-link'></i>
236
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L237' id='L237' rel='#L237'>
<i class='fa fa-link'></i>
237
</a>
<!-- We're not using `link_to` because it is too slow once we get to thousands of lines. -->
<a href='#L238' id='L238' rel='#L238'>
<i class='fa fa-link'></i>
238
</a>
</div>
<pre class="code highlight"><code><span id="LC1" class="line"><span class="cp">&lt;?php</span></span>&#x000A;<span id="LC2" class="line"><span class="c1">// AuthWP.php</span>&#x000A;<span id="LC3" class="line">// MediaWiki extension to delegate authentication and user management</span>&#x000A;<span id="LC4" class="line">// to a local Wordpress installation.</span>&#x000A;<span id="LC5" class="line">// See http://ciarang.com/wiki/page/WPMW for more information.</span>&#x000A;<span id="LC6" class="line">// Version 0.3.1</span>&#x000A;<span id="LC7" class="line">// Copyright (C) 2008-13 Ciaran Gultnieks &lt;ciaran@ciarang.com&gt;</span>&#x000A;<span id="LC8" class="line">//</span>&#x000A;<span id="LC9" class="line">// This program is free software: you can redistribute it and/or modify</span>&#x000A;<span id="LC10" class="line">// it under the terms of the GNU Affero General Public License as published by</span>&#x000A;<span id="LC11" class="line">// the Free Software Foundation, either version 3 of the License, or</span>&#x000A;<span id="LC12" class="line">// (at your option) any later version.</span>&#x000A;<span id="LC13" class="line">//</span>&#x000A;<span id="LC14" class="line">// This program is distributed in the hope that it will be useful,</span>&#x000A;<span id="LC15" class="line">// but WITHOUT ANY WARRANTY; without even the implied warranty of</span>&#x000A;<span id="LC16" class="line">// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the</span>&#x000A;<span id="LC17" class="line">// GNU Affero General Public License for more details.</span>&#x000A;<span id="LC18" class="line">//</span>&#x000A;<span id="LC19" class="line">// You should have received a copy of the GNU Affero General Public License</span>&#x000A;<span id="LC20" class="line">// along with this program.  If not, see &lt;http://www.gnu.org/licenses/&gt;.</span>&#x000A;<span id="LC21" class="line">//</span>&#x000A;<span id="LC22" class="line"></span></span>&#x000A;<span id="LC23" class="line"><span class="k">if</span><span class="p">(</span><span class="o">!</span><span class="nb">defined</span><span class="p">(</span><span class="s1">&#39;MEDIAWIKI&#39;</span><span class="p">))</span> <span class="p">{</span></span>&#x000A;<span id="LC24" class="line">    <span class="k">die</span><span class="p">(</span><span class="s1">&#39;Not an entry point.&#39;</span><span class="p">);</span></span>&#x000A;<span id="LC25" class="line"><span class="p">}</span></span>&#x000A;<span id="LC26" class="line"></span>&#x000A;<span id="LC27" class="line"><span class="c1">// Relative path to Wordpress installation. In the default &#39;..&#39; we</span>&#x000A;<span id="LC28" class="line">// have MediaWiki installed in a &#39;wiki&#39; directory off the main</span>&#x000A;<span id="LC29" class="line">// Wordpress root.</span>&#x000A;<span id="LC30" class="line"></span><span class="nv">$WP_relpath</span><span class="o">=</span><span class="nb">isset</span><span class="p">(</span><span class="nv">$wgAuthWPRelPath</span><span class="p">)</span><span class="o">?</span><span class="nv">$wgAuthWPRelPath</span><span class="o">:</span><span class="s1">&#39;..&#39;</span><span class="p">;</span></span>&#x000A;<span id="LC31" class="line"></span>&#x000A;<span id="LC32" class="line"></span>&#x000A;<span id="LC33" class="line"><span class="c1">// We&#39;ll derive our class from MediaWiki&#39;s AuthPlugin class...</span>&#x000A;<span id="LC34" class="line"></span><span class="k">require_once</span><span class="p">(</span><span class="s2">&quot;</span><span class="nv">$IP</span><span class="s2">/includes/AuthPlugin.php&quot;</span><span class="p">);</span></span>&#x000A;<span id="LC35" class="line"></span>&#x000A;<span id="LC36" class="line"></span>&#x000A;<span id="LC37" class="line"><span class="c1">// Bootstrap Wordpress. This seems rather foolish since surely the</span>&#x000A;<span id="LC38" class="line">// names of things are bound to clash somewhere, but we want to be</span>&#x000A;<span id="LC39" class="line">// able to handle everything as if Wordpress was doing it natively</span>&#x000A;<span id="LC40" class="line">// including respecting any plugins that might be in place.</span>&#x000A;<span id="LC41" class="line"></span><span class="k">if</span><span class="p">(</span><span class="nb">php_sapi_name</span><span class="p">()</span> <span class="o">!=</span> <span class="s1">&#39;cli&#39;</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC42" class="line">	<span class="k">require</span><span class="p">(</span><span class="nv">$WP_relpath</span><span class="o">.</span><span class="s1">&#39;/wp-load.php&#39;</span><span class="p">);</span></span>&#x000A;<span id="LC43" class="line">	<span class="k">require</span><span class="p">(</span><span class="nv">$WP_relpath</span><span class="o">.</span><span class="s1">&#39;/wp-includes/registration.php&#39;</span><span class="p">);</span></span>&#x000A;<span id="LC44" class="line"><span class="p">}</span></span>&#x000A;<span id="LC45" class="line"></span>&#x000A;<span id="LC46" class="line"><span class="c1">// Wordpress has escaped all these in wp-settings.php - we need to</span>&#x000A;<span id="LC47" class="line">// unescape them again if they weren&#39;t meant to be escaped.</span>&#x000A;<span id="LC48" class="line"></span><span class="k">if</span><span class="p">(</span><span class="nb">php_sapi_name</span><span class="p">()</span> <span class="o">!=</span> <span class="s1">&#39;cli&#39;</span> <span class="o">&amp;&amp;</span> <span class="o">!</span><span class="nb">get_magic_quotes_gpc</span><span class="p">())</span> <span class="p">{</span></span>&#x000A;<span id="LC49" class="line">	<span class="nv">$_GET</span>    <span class="o">=</span> <span class="nx">stripslashes_deep</span><span class="p">(</span><span class="nv">$_GET</span>   <span class="p">);</span></span>&#x000A;<span id="LC50" class="line">	<span class="nv">$_POST</span>   <span class="o">=</span> <span class="nx">stripslashes_deep</span><span class="p">(</span><span class="nv">$_POST</span>  <span class="p">);</span></span>&#x000A;<span id="LC51" class="line">	<span class="nv">$_COOKIE</span> <span class="o">=</span> <span class="nx">stripslashes_deep</span><span class="p">(</span><span class="nv">$_COOKIE</span><span class="p">);</span></span>&#x000A;<span id="LC52" class="line"><span class="p">}</span></span>&#x000A;<span id="LC53" class="line"></span>&#x000A;<span id="LC54" class="line"><span class="nv">$wgExtensionCredits</span><span class="p">[</span><span class="s1">&#39;other&#39;</span><span class="p">][]</span> <span class="o">=</span> <span class="k">array</span><span class="p">(</span></span>&#x000A;<span id="LC55" class="line">	<span class="s1">&#39;path&#39;</span> <span class="o">=&gt;</span> <span class="k">__FILE__</span><span class="p">,</span></span>&#x000A;<span id="LC56" class="line">	<span class="s1">&#39;name&#39;</span> <span class="o">=&gt;</span> <span class="s1">&#39;WPMW&#39;</span><span class="p">,</span></span>&#x000A;<span id="LC57" class="line">	<span class="s1">&#39;version&#39;</span> <span class="o">=&gt;</span> <span class="s1">&#39;0.3.1&#39;</span><span class="p">,</span></span>&#x000A;<span id="LC58" class="line">	<span class="s1">&#39;author&#39;</span> <span class="o">=&gt;</span> <span class="s1">&#39;Ciaran Gultnieks&#39;</span><span class="p">,</span></span>&#x000A;<span id="LC59" class="line">	<span class="s1">&#39;url&#39;</span> <span class="o">=&gt;</span> <span class="s1">&#39;https://www.mediawiki.org/wiki/Extension:WPMW&#39;</span><span class="p">,</span></span>&#x000A;<span id="LC60" class="line">        <span class="s1">&#39;descriptionmsg&#39;</span> <span class="o">=&gt;</span> <span class="s1">&#39;Provides WordPress login integration&#39;</span><span class="p">,</span></span>&#x000A;<span id="LC61" class="line"><span class="p">);</span></span>&#x000A;<span id="LC62" class="line"></span>&#x000A;<span id="LC63" class="line"><span class="c1">// Handler for the MediaWiki UserLoadFromSession hook. Allows users</span>&#x000A;<span id="LC64" class="line">// already signed in to Wordpress to be automatically signed in to</span>&#x000A;<span id="LC65" class="line">// MediaWiki. Always returns true, but sets $result to true if auth</span>&#x000A;<span id="LC66" class="line">// has been done.</span>&#x000A;<span id="LC67" class="line"></span><span class="k">function</span> <span class="nf">AuthWPUserLoadFromSession</span><span class="p">(</span><span class="nv">$user</span><span class="p">,</span> <span class="o">&amp;</span><span class="nv">$result</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC68" class="line"></span>&#x000A;<span id="LC69" class="line">        <span class="c1">// Abort in cli mode. Seems like it shouldn&#39;t be necessary</span>&#x000A;<span id="LC70" class="line"></span>        <span class="c1">// but some cli scripts to end up here for whatever bizarre</span>&#x000A;<span id="LC71" class="line"></span>        <span class="c1">// reason - runjobs is an example.</span>&#x000A;<span id="LC72" class="line"></span>        <span class="k">if</span><span class="p">(</span><span class="nb">php_sapi_name</span><span class="p">()</span> <span class="o">==</span> <span class="s1">&#39;cli&#39;</span><span class="p">)</span></span>&#x000A;<span id="LC73" class="line">            <span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC74" class="line"></span>&#x000A;<span id="LC75" class="line">	<span class="c1">// Is there a Wordpress user with a valid session?</span>&#x000A;<span id="LC76" class="line"></span>	<span class="nv">$wpuser</span><span class="o">=</span><span class="nx">wp_get_current_user</span><span class="p">();</span></span>&#x000A;<span id="LC77" class="line">	<span class="k">if</span><span class="p">(</span><span class="o">!</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">ID</span><span class="p">)</span></span>&#x000A;<span id="LC78" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC79" class="line"></span>&#x000A;<span id="LC80" class="line">	<span class="nv">$u</span><span class="o">=</span><span class="nx">User</span><span class="o">::</span><span class="na">newFromName</span><span class="p">(</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_login</span><span class="p">);</span></span>&#x000A;<span id="LC81" class="line">	<span class="k">if</span><span class="p">(</span><span class="o">!</span><span class="nv">$u</span><span class="p">)</span></span>&#x000A;<span id="LC82" class="line">		<span class="nx">wp_die</span><span class="p">(</span><span class="s2">&quot;Your username &#39;&quot;</span><span class="o">.</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_login</span><span class="o">.</span><span class="s2">&quot;&#39; is not a valid MediaWiki username&quot;</span><span class="p">);</span></span>&#x000A;<span id="LC83" class="line">	<span class="k">if</span><span class="p">(</span><span class="mi">0</span><span class="o">==</span><span class="nv">$u</span><span class="o">-&gt;</span><span class="na">getID</span><span class="p">())</span> <span class="p">{</span></span>&#x000A;<span id="LC84" class="line">		<span class="nv">$u</span><span class="o">-&gt;</span><span class="na">addToDatabase</span><span class="p">();</span></span>&#x000A;<span id="LC85" class="line">		<span class="nv">$u</span><span class="o">-&gt;</span><span class="na">setToken</span><span class="p">();</span></span>&#x000A;<span id="LC86" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC87" class="line">	<span class="nv">$id</span><span class="o">=</span><span class="nx">User</span><span class="o">::</span><span class="na">idFromName</span><span class="p">(</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_login</span><span class="p">);</span></span>&#x000A;<span id="LC88" class="line">	<span class="k">if</span><span class="p">(</span><span class="o">!</span><span class="nv">$id</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC89" class="line">		<span class="nx">wp_die</span><span class="p">(</span><span class="s2">&quot;Failed to get ID from name &#39;&quot;</span><span class="o">.</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_login</span><span class="o">.</span><span class="s2">&quot;&#39;&quot;</span><span class="p">);</span></span>&#x000A;<span id="LC90" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC91" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC92" class="line">	<span class="k">if</span><span class="p">(</span><span class="nv">$id</span><span class="o">==</span><span class="mi">0</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC93" class="line">		<span class="nx">wp_die</span><span class="p">(</span><span class="s2">&quot;Wikipedia &#39;&quot;</span><span class="o">.</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_login</span><span class="o">.</span><span class="s2">&quot;&#39; was not found.&quot;</span><span class="p">);</span></span>&#x000A;<span id="LC94" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC95" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC96" class="line">	<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">setID</span><span class="p">(</span><span class="nv">$id</span><span class="p">);</span></span>&#x000A;<span id="LC97" class="line">	<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">loadFromId</span><span class="p">();</span></span>&#x000A;<span id="LC98" class="line">	<span class="nx">wfSetupSession</span><span class="p">();</span>	</span>&#x000A;<span id="LC99" class="line">	<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">setCookies</span><span class="p">();</span></span>&#x000A;<span id="LC100" class="line"></span>&#x000A;<span id="LC101" class="line">	<span class="c1">// Set these to ensure synchronisation with WordPress...</span>&#x000A;<span id="LC102" class="line"></span>	<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">setEmail</span><span class="p">(</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_email</span><span class="p">);</span></span>&#x000A;<span id="LC103" class="line">	<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">setRealName</span><span class="p">(</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_nicename</span><span class="p">);</span></span>&#x000A;<span id="LC104" class="line"></span>&#x000A;<span id="LC105" class="line">	<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">saveSettings</span><span class="p">();</span></span>&#x000A;<span id="LC106" class="line">	<span class="nv">$result</span><span class="o">=</span><span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC107" class="line"></span>&#x000A;<span id="LC108" class="line">	<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC109" class="line"><span class="p">}</span></span>&#x000A;<span id="LC110" class="line"></span>&#x000A;<span id="LC111" class="line"><span class="c1">// Handler for the MediaWiki UserLogout hook.</span>&#x000A;<span id="LC112" class="line"></span><span class="k">function</span> <span class="nf">AuthWPUserLogout</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$user</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC113" class="line">	<span class="c1">// Log out of Wordpress as well...</span>&#x000A;<span id="LC114" class="line"></span>	<span class="nx">wp_logout</span><span class="p">();</span></span>&#x000A;<span id="LC115" class="line">	<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC116" class="line"><span class="p">}</span></span>&#x000A;<span id="LC117" class="line"></span>&#x000A;<span id="LC118" class="line"><span class="k">class</span> <span class="nc">AuthWP</span> <span class="k">extends</span> <span class="nx">AuthPlugin</span> <span class="p">{</span></span>&#x000A;<span id="LC119" class="line"></span>&#x000A;<span id="LC120" class="line">	<span class="c1">// Constructor</span>&#x000A;<span id="LC121" class="line"></span>	<span class="k">function</span> <span class="nf">AuthWP</span><span class="p">(){</span></span>&#x000A;<span id="LC122" class="line"></span>&#x000A;<span id="LC123" class="line">		<span class="c1">// Add hooks...</span>&#x000A;<span id="LC124" class="line"></span>		<span class="k">global</span> <span class="nv">$wgHooks</span><span class="p">;</span></span>&#x000A;<span id="LC125" class="line">		<span class="nv">$wgHooks</span><span class="p">[</span><span class="s1">&#39;UserLoadFromSession&#39;</span><span class="p">][]</span><span class="o">=</span><span class="s1">&#39;AuthWPUserLoadFromSession&#39;</span><span class="p">;</span></span>&#x000A;<span id="LC126" class="line">		<span class="nv">$wgHooks</span><span class="p">[</span><span class="s1">&#39;UserLogout&#39;</span><span class="p">][]</span> <span class="o">=</span> <span class="s1">&#39;AuthWPUserLogout&#39;</span><span class="p">;</span></span>&#x000A;<span id="LC127" class="line"></span>&#x000A;<span id="LC128" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC129" class="line"></span>&#x000A;<span id="LC130" class="line"></span>&#x000A;<span id="LC131" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC132" class="line"></span>	<span class="c1">// See if the given user exists - true if so, false if not...</span>&#x000A;<span id="LC133" class="line"></span>	<span class="k">function</span> <span class="nf">userExists</span><span class="p">(</span><span class="nv">$username</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC134" class="line">		<span class="k">return</span> <span class="nx">username_exists</span><span class="p">(</span><span class="nv">$username</span><span class="p">);</span></span>&#x000A;<span id="LC135" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC136" class="line"></span>&#x000A;<span id="LC137" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC138" class="line"></span>	<span class="c1">// Handle authentication, returning true if the given credentials</span>&#x000A;<span id="LC139" class="line"></span>	<span class="c1">// are good, or false if they&#39;re bad.</span>&#x000A;<span id="LC140" class="line"></span>	<span class="k">function</span> <span class="nf">authenticate</span><span class="p">(</span><span class="nv">$username</span><span class="p">,</span><span class="nv">$password</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC141" class="line">		<span class="nv">$credentials</span><span class="o">=</span><span class="k">array</span><span class="p">(</span><span class="s1">&#39;user_login&#39;</span><span class="o">=&gt;</span><span class="nv">$username</span><span class="p">,</span><span class="s1">&#39;user_password&#39;</span><span class="o">=&gt;</span><span class="nv">$password</span><span class="p">);</span></span>&#x000A;<span id="LC142" class="line">		<span class="k">if</span><span class="p">(</span><span class="nx">is_wp_error</span><span class="p">(</span><span class="nx">wp_signon</span><span class="p">(</span><span class="nv">$credentials</span><span class="p">,</span><span class="kc">false</span><span class="p">)))</span></span>&#x000A;<span id="LC143" class="line">			<span class="k">return</span> <span class="kc">false</span><span class="p">;</span></span>&#x000A;<span id="LC144" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC145" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC146" class="line"></span>&#x000A;<span id="LC147" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC148" class="line"></span>	<span class="c1">// Modify the login template...</span>&#x000A;<span id="LC149" class="line"></span>	<span class="k">function</span> <span class="nf">modifyUITemplate</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$template</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC150" class="line">		<span class="nv">$template</span><span class="o">-&gt;</span><span class="na">set</span><span class="p">(</span><span class="s1">&#39;create&#39;</span><span class="p">,</span><span class="kc">false</span><span class="p">);</span></span>&#x000A;<span id="LC151" class="line">		<span class="nv">$template</span><span class="o">-&gt;</span><span class="na">set</span><span class="p">(</span><span class="s1">&#39;usedomain&#39;</span><span class="p">,</span><span class="kc">false</span><span class="p">);</span></span>&#x000A;<span id="LC152" class="line">		<span class="nv">$template</span><span class="o">-&gt;</span><span class="na">set</span><span class="p">(</span><span class="s1">&#39;useemail&#39;</span><span class="p">,</span><span class="kc">true</span><span class="p">);</span></span>&#x000A;<span id="LC153" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC154" class="line"></span>&#x000A;<span id="LC155" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC156" class="line"></span>	<span class="c1">// Always return true - tells it to automatically create a local</span>&#x000A;<span id="LC157" class="line"></span>	<span class="c1">// account when asked to log in a user that doesn&#39;t exist locally.</span>&#x000A;<span id="LC158" class="line"></span>	<span class="k">function</span> <span class="nf">autoCreate</span><span class="p">()</span> <span class="p">{</span></span>&#x000A;<span id="LC159" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC160" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC161" class="line"></span>&#x000A;<span id="LC162" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC163" class="line"></span>	<span class="k">function</span> <span class="nf">allowEmailChange</span><span class="p">()</span> <span class="p">{</span></span>&#x000A;<span id="LC164" class="line">		<span class="c1">// No - change it via the WordPress interface only.</span>&#x000A;<span id="LC165" class="line"></span>		<span class="k">return</span> <span class="kc">false</span><span class="p">;</span></span>&#x000A;<span id="LC166" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC167" class="line"></span>&#x000A;<span id="LC168" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC169" class="line"></span>	<span class="k">function</span> <span class="nf">allowRealNameChange</span><span class="p">()</span> <span class="p">{</span></span>&#x000A;<span id="LC170" class="line">		<span class="c1">// No - change it via the WordPress interface only.</span>&#x000A;<span id="LC171" class="line"></span>		<span class="k">return</span> <span class="kc">false</span><span class="p">;</span></span>&#x000A;<span id="LC172" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC173" class="line"></span>&#x000A;<span id="LC174" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC175" class="line"></span>	<span class="c1">// Always return true - users can change their passwords from</span>&#x000A;<span id="LC176" class="line"></span>	<span class="c1">// MediaWiki - we&#39;ll hash them and update the Wordpress DB.</span>&#x000A;<span id="LC177" class="line"></span>	<span class="k">function</span> <span class="nf">allowPasswordChange</span><span class="p">()</span> <span class="p">{</span></span>&#x000A;<span id="LC178" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC179" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC180" class="line"></span>&#x000A;<span id="LC181" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC182" class="line"></span>	<span class="c1">// Set a new password for the given user...</span>&#x000A;<span id="LC183" class="line"></span>	<span class="k">function</span> <span class="nf">setPassword</span><span class="p">(</span><span class="nv">$user</span><span class="p">,</span><span class="nv">$password</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC184" class="line">		<span class="nv">$wpuser</span><span class="o">=</span><span class="nx">get_userdatabylogin</span><span class="p">(</span><span class="nv">$user</span><span class="o">-&gt;</span><span class="na">mName</span><span class="p">);</span></span>&#x000A;<span id="LC185" class="line">		<span class="k">if</span><span class="p">(</span><span class="o">!</span><span class="nv">$wpuser</span><span class="p">)</span></span>&#x000A;<span id="LC186" class="line">			<span class="k">return</span> <span class="kc">false</span><span class="p">;</span></span>&#x000A;<span id="LC187" class="line">		<span class="nx">wp_set_password</span><span class="p">(</span><span class="nv">$password</span><span class="p">,</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_id</span><span class="p">);</span></span>&#x000A;<span id="LC188" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC189" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC190" class="line"></span>&#x000A;<span id="LC191" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC192" class="line"></span>	<span class="c1">// Update the details of a user that&#39;s logging in - i.e. fill in any</span>&#x000A;<span id="LC193" class="line"></span>	<span class="c1">// details we can retrieve from the Wordpress user details...</span>&#x000A;<span id="LC194" class="line"></span>	<span class="k">function</span> <span class="nf">updateUser</span><span class="p">(</span><span class="o">&amp;</span><span class="nv">$user</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC195" class="line">		<span class="nv">$wpuser</span><span class="o">=</span><span class="nx">get_userdatabylogin</span><span class="p">(</span><span class="nv">$user</span><span class="o">-&gt;</span><span class="na">mName</span><span class="p">);</span></span>&#x000A;<span id="LC196" class="line">		<span class="k">if</span><span class="p">(</span><span class="o">!</span><span class="nv">$wpuser</span><span class="p">)</span></span>&#x000A;<span id="LC197" class="line">			<span class="k">return</span> <span class="kc">false</span><span class="p">;</span></span>&#x000A;<span id="LC198" class="line">		<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">setEmail</span><span class="p">(</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_email</span><span class="p">);</span></span>&#x000A;<span id="LC199" class="line">		<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">setRealName</span><span class="p">(</span><span class="nv">$wpuser</span><span class="o">-&gt;</span><span class="na">user_nicename</span><span class="p">);</span></span>&#x000A;<span id="LC200" class="line">		<span class="nv">$user</span><span class="o">-&gt;</span><span class="na">saveSettings</span><span class="p">();</span></span>&#x000A;<span id="LC201" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC202" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC203" class="line"></span>&#x000A;<span id="LC204" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC205" class="line"></span>	<span class="c1">// Update user details in Wordpress database...</span>&#x000A;<span id="LC206" class="line"></span>	<span class="k">function</span> <span class="nf">updateExternalDB</span><span class="p">(</span><span class="nv">$user</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC207" class="line">		<span class="c1">// Not doing anything here (yet?)</span>&#x000A;<span id="LC208" class="line"></span>		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC209" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC210" class="line"></span>&#x000A;<span id="LC211" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC212" class="line"></span>	<span class="c1">// Add a user created in MediaWiki to the Wordpress database...</span>&#x000A;<span id="LC213" class="line"></span>	<span class="k">function</span> <span class="nf">addUser</span><span class="p">(</span><span class="nv">$user</span><span class="p">,</span><span class="nv">$password</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC214" class="line">		<span class="nx">wp_create_user</span><span class="p">(</span><span class="nv">$user</span><span class="o">-&gt;</span><span class="na">mName</span><span class="p">,</span><span class="nv">$password</span><span class="p">,</span><span class="nv">$user</span><span class="o">-&gt;</span><span class="na">mEmail</span><span class="p">);</span></span>&#x000A;<span id="LC215" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC216" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC217" class="line"></span>&#x000A;<span id="LC218" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC219" class="line"></span>	<span class="c1">// Just return true meaning that logins can only be authenticated in</span>&#x000A;<span id="LC220" class="line"></span>	<span class="c1">// this module, and not checked against the mediawiki db...</span>&#x000A;<span id="LC221" class="line"></span>	<span class="k">function</span> <span class="nf">strict</span><span class="p">()</span> <span class="p">{</span></span>&#x000A;<span id="LC222" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC223" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC224" class="line"></span>&#x000A;<span id="LC225" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC226" class="line"></span>	<span class="c1">// As with strict(), only authenticate through this plugin.</span>&#x000A;<span id="LC227" class="line"></span>	<span class="k">function</span> <span class="nf">strictUserAuth</span><span class="p">(</span><span class="nv">$username</span><span class="p">)</span> <span class="p">{</span></span>&#x000A;<span id="LC228" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC229" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC230" class="line"></span>&#x000A;<span id="LC231" class="line">	<span class="c1">// MediaWiki API HANDLER</span>&#x000A;<span id="LC232" class="line"></span>	<span class="c1">// We can create external accounts so always return true...</span>&#x000A;<span id="LC233" class="line"></span>	<span class="k">function</span> <span class="nf">canCreateAccounts</span><span class="p">()</span> <span class="p">{</span></span>&#x000A;<span id="LC234" class="line">		<span class="k">return</span> <span class="kc">true</span><span class="p">;</span></span>&#x000A;<span id="LC235" class="line">	<span class="p">}</span></span>&#x000A;<span id="LC236" class="line"></span>&#x000A;<span id="LC237" class="line"><span class="p">}</span></span>&#x000A;<span id="LC238" class="line"><span class="cp">?&gt;</span></span></code></pre>&#x000A;
</div>

</div>

</article>
</div>

</div>

</div>
</div>
</div>
</div>
</div>



</body>
</html>

