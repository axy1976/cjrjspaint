<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title><?= $title ?></title>

	<!-- This should mirror CSP in electron-main.js, except maybe for firebase stuff. -->
	<!-- Firebase stuff is somewhat speculative, as the quota is exceeded as I'm adding this. -->
	<!-- Lax img-src is needed for speech recognition, e.g. interpret_command("draw a cat")[0].exec(); -->
	<!-- connect-src needs data:/blob: for loading images via fetch, including from local storage. -->
	<!-- <meta http-equiv="Content-Security-Policy" content="
		default-src 'self';
		script-src 'self' https://jspaint.firebaseio.com;
		style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
		img-src 'self' data: blob: http: https:;
		font-src 'self' https://fonts.gstatic.com;
		connect-src * data: blob: https://jspaint.firebaseio.com wss://jspaint.firebaseio.com;
	"> -->

	<link href="<?php echo base_url()?>application/views/jspaint/styles/normalize.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url()?>application/views/jspaint/styles/layout.css" class="flippable-layout-stylesheet" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url()?>application/views/jspaint/styles/print.css" rel="stylesheet" type="text/css" media="print">
	<link href="<?php echo base_url()?>application/views/jspaint/lib/os-gui/layout.css" class="flippable-layout-stylesheet" rel="stylesheet" type="text/css">
	<!-- <link href="<?php echo base_url()?>application/views/jspaint/lib/os-gui/windows-98.css" rel="stylesheet" type="text/css"> -->
	<!-- <link href="<?php echo base_url()?>application/views/jspaint/lib/os-gui/windows-default.css" rel="stylesheet" type="text/css" title="Windows Default"> -->
	<!-- <link href="<?php echo base_url()?>application/views/jspaint/lib/os-gui/peggys-pastels.css" rel="alternate stylesheet" type="text/css" title="Peggy's Pastels"> -->
	<!-- <link href="<?php echo base_url()?>application/views/jspaint/lib/tracky-mouse/tracky-mouse.css" rel="stylesheet" type="text/css"> -->
	<!--
		@TODO: bring these styles into OS-GUI.
		This is a custom build of 98.css https://github.com/jdan/98.css
		for checkboxes, radio buttons, sliders, and fieldsets,
		excluding e.g. scrollbars, buttons, and windows (already in OS-GUI),
		and integrating with the theme CSS vars used by OS-GUI,
		and with some RTLCSS tweaks.
		Text inputs and dropdowns are styled in classic.css, but should also be included in OS-GUI at some point.
		This is not an @import in classic.css because it needs RTLCSS and I'm not applying RTLCSS to themes yet.
		So I added .not-for-modern logic to theme.js to exclude these styles depending on the theme.
	-->
	<link href="<?php echo base_url()?>application/views/jspaint/lib/98.css/98.custom-build.css" class="flippable-layout-stylesheet not-for-modern" rel="stylesheet"
		type="text/css">

	<link rel="apple-touch-icon" href="<?= $favicon ?>">
	<!-- Chrome will pick the largest image for some reason, instead of the most appropriate one. -->
	<!-- <link rel="icon" type="image/png" sizes="192x192" href="images/icons/192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="images/icons/32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="images/icons/96x96.png"> -->
	<!-- <link rel="icon" type="image/png" sizes="16x16" href="images/icons/16x16.png"> -->
	<link rel="manifest" href="manifest.webmanifest">
	<meta name="msapplication-TileColor" content="#008080">
	<meta name="msapplication-TileImage" content="<?= $favicon ?>">
	<meta name="theme-color" content="#000080">

	<meta name="viewport" content="width=device-width, user-scalable=no">

	<meta name="description" content="Paint in the browser, with extra features" />
	<meta property="og:image:width" content="279">
	<meta property="og:image:height" content="279">
	<meta property="og:description" content="Paint in the browser, with extra features.">
	<meta property="og:title" content="<?= $title ?>">
	<meta property="og:image" content="<?= $favicon ?>">
	<meta name="twitter:title" content="<?= $title ?>">
	<meta name="twitter:description" content="Paint in the browser, with extra features">
	<meta name="twitter:image" content="<?= $favicon ?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@code4bots">
	<meta name="twitter:creator" content="@code4bots">

	<script src="<?php echo base_url()?>application/views/jspaint/src/error-handling-basic.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/theme.js"></script>
</head>

<body>
	<div id="about-paint" style="display: none">
		<h1 id="about-paint-header">
			<img src="<?= $favicon ?>" width="32" height="32" id="paint-32x32" alt="" />
			<span id="jspaint-project-name"><?= $title ?></span>
		</h1>

		<div id="maybe-outdated-view-project-news" hidden>
			<div id="maybe-outdated-line">
				<div id="outdated" hidden>
					<div class="on-official-host">
						There's a new version of <?= $title ?>. <a id="refresh-to-update" href=".">Refresh</a> to get it.
					</div>
				</div>
				<div id="checking-for-updates" hidden>
					Checking for updates...
				</div>
				<div id="failed-to-check-if-outdated" hidden>
					Couldn't check for updates.
					<span class="navigator-offline">You're offline.</span>
					<span class="navigator-online"><?= $title ?> may be outdated.</span>
				</div>
			</div>
		</div>
	</div>
	<script defer src="<?php echo base_url()?>application/views/jspaint/src/test-news.js"></script>
	<!--
		Before publishing a news update, make sure:
		- The <time> element matches the date of the update.
		- The id of the <article> will never need to be changed.
			I'm using the format "news-YYYY-very-brief-description".
			Avoiding putting the date in the id, in case I push the update later.
			The id is used to check for updates, and is stored in localStorage.
		- The console shows no errors.
			test-news.js checks for some problems.
		- HTML is valid.
	-->

	<!-- Note: no CDNs, even with fallback, as the fallback is too complicated to handle with CSP. -->
	<script src="<?php echo base_url()?>application/views/jspaint/lib/jquery-3.4.1.min.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/gif.js/gif.js"></script>
	<!-- pako is used by UPNG.js and UTIF.js -->
	<script src="<?php echo base_url()?>application/views/jspaint/lib/pako-2.0.3.min.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/UPNG.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/UTIF.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/bmp.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/pdf.js/build/pdf.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/anypalette-0.6.0.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/FileSaver.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/font-detective.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/libtess.min.js"></script>
	<!-- <script src="<?php echo base_url()?>application/views/jspaint/lib/tracky-mouse/tracky-mouse.js"></script> -->
	<script src="<?php echo base_url()?>application/views/jspaint/lib/os-gui/parse-theme.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/os-gui/$Window.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/os-gui/MenuBar.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/imagetracer_v1.2.5.js"></script>

	<!-- must not be async/deferred, as it uses document.write(); and must come before other app code which uses localization functions -->
	<script src="<?php echo base_url()?>application/views/jspaint/src/app-localization.js"></script>

	<script src="<?php echo base_url()?>application/views/jspaint/src/msgbox.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/functions.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/helpers.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/storage.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/$Component.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/$ToolWindow.js"></script>

	<!-- After show_error_message, showMessageBox, make_window_supporting_scale, and localize are defined,
	set up better global error handling. -->
	<!-- Note: This must be in the <body> as it also handles showing a message for Internet Explorer. -->
	<script src="<?php echo base_url()?>application/views/jspaint/src/error-handling-enhanced.js"></script>

	<script src="<?php echo base_url()?>application/views/jspaint/src/$ToolBox.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/$ColorBox.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/$FontBox.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/Handles.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/OnCanvasObject.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/OnCanvasSelection.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/OnCanvasTextBox.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/OnCanvasHelperLayer.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/image-manipulation.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/tool-options.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/tools.js"></script>
	<!--<script src="<?php echo base_url()?>application/views/jspaint/src/extra-tools.js"></script>-->
	<script src="<?php echo base_url()?>application/views/jspaint/src/edit-colors.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/manage-storage.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/imgur.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/help.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/simulate-random-gestures.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/menus.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/speech-recognition.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/app.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/sessions.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/lib/konami.js"></script>
	<script src="<?php echo base_url()?>application/views/jspaint/src/vaporwave-fun.js"></script>

	<noscript>
		<h1><img src="<?= $favicon ?>" width="32" height="32" alt="<?= $title ?>" /><?= $title ?></h1>

		<p>This application requires JavaScript to run.</p>

		<!-- <p>
			Assuming this is the official instance of jspaint,
			at <a href="https://jspaint.app">https://jspaint.app</a>,
			you can safely enable JavaScript.
		</p>

		<p>You can also check out <a href="https://github.com/1j01/jspaint">the source code and project info</a>.</p> -->
	</noscript>

	<svg style="position: absolute; pointer-events: none; bottom: 100%;">
		<defs>
			<filter id="disabled-inset-filter" x="0" y="0" width="1px" height="1px">
				<feColorMatrix in="SourceGraphic" type="matrix" values="
					1 0 0 0 0
					0 1 0 0 0
					0 0 1 0 0
					-1000 -1000 -1000 1 0
				" result="black-parts-isolated" />
				<feFlood result="shadow-color" flood-color="var(--ButtonShadow)" />
				<feFlood result="hilight-color" flood-color="var(--ButtonHilight)" />
				<feOffset in="black-parts-isolated" dx="1" dy="1" result="offset" />
				<feComposite in="hilight-color" in2="offset" operator="in" result="hilight-colored-offset" />
				<feComposite in="shadow-color" in2="black-parts-isolated" operator="in" result="shadow-colored" />
				<feMerge>
					<feMergeNode in="hilight-colored-offset" />
					<feMergeNode in="shadow-colored" />
				</feMerge>
			</filter>
			<filter id="disabled-inset-filter-2" x="0" y="0" width="1px" height="1px">
				<feColorMatrix in="SourceGraphic" type="matrix" values="
					1 0 0 0 0
					0 1 0 0 0
					0 0 1 0 0
					-1 -1 -0 1 0
				" result="black-and-blue-parts-isolated" />
				<feFlood result="shadow-color" flood-color="var(--ButtonShadow)" />
				<feFlood result="hilight-color" flood-color="var(--ButtonHilight)" />
				<feOffset in="black-and-blue-parts-isolated" dx="1" dy="1" result="offset" />
				<feComposite in="hilight-color" in2="offset" operator="in" result="hilight-colored-offset" />
				<feComposite in="shadow-color" in2="black-and-blue-parts-isolated" operator="in"
					result="shadow-colored" />
				<feMerge>
					<feMergeNode in="hilight-colored-offset" />
					<feMergeNode in="shadow-colored" />
				</feMerge>
			</filter>
		</defs>
	</svg>
</body>

</html>