<?php
require("../../../vendor/autoload.php");
?>
<html>
<head>
    <title>Restler API Explorer</title>
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'/>
    <link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    <link href='css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
    <script src='lib/jquery-1.8.0.min.js' type='text/javascript'></script>
    <script src='lib/jquery.slideto.min.js' type='text/javascript'></script>
    <script src='lib/jquery.wiggle.min.js' type='text/javascript'></script>
    <script src='lib/jquery.ba-bbq.min.js' type='text/javascript'></script>
    <script src='lib/handlebars-1.0.rc.1.js' type='text/javascript'></script>
    <script src='lib/underscore-min.js' type='text/javascript'></script>
    <script src='lib/backbone-min.js' type='text/javascript'></script>
    <script src='lib/swagger.js' type='text/javascript'></script>
    <script src='swagger-ui.js' type='text/javascript'></script>

    <style type="text/css">
        .swagger-ui-wrap {
            max-width: 960px;
            margin-left: auto;
            margin-right: auto;
        }

        .icon-btn {
            cursor: pointer;
        }

        #message-bar {
            min-height: 30px;
            text-align: center;
            padding-top: 10px;
        }

        .message-success {
            color: #89BF04;
        }

        .message-fail {
            color: #cc0000;
        }

		#header label {
			color: white;
			font-size: 0.8em;
		}
    </style>

	<script type="text/javascript">
        var swagger = function (version = null) {
            if (version == null) version = 1;
            window.swaggerUi = new SwaggerUi({
                discoveryUrl: "../v" + version + "/resources.json",
                apiKey: "",
                dom_id: "swagger-ui-container",
                supportHeaderParams: false,
                supportedSubmitMethods: ['get', 'post', 'put', 'patch', 'delete'],
                onComplete: function (swaggerApi, swaggerUi) {
                    if (console) {
                        console.log("Loaded SwaggerUI");
                        console.log(swaggerApi);
                        console.log(swaggerUi);
                    }
                },
                onFailure: function (data) {
                    if (console) {
                        console.log("Unable to Load SwaggerUI");
                        console.log(data);
                    }
                },
                docExpansion: "none"
            });

            window.swaggerUi.load();
        };

        $(swagger(1));
	</script>
</head>

<body>
<div id='header'>
    <div class="swagger-ui-wrap">
        <a id="logo" href="https://github.com/Luracast/Restler-API-Explorer" target="_blank">API Explorer</a>
        <form id='api_selector'>
            <div class='input'><input placeholder="http://example.com/api" id="input_baseUrl" name="baseUrl" type="hidden"/></div>
			<div class='input'>
				<label>
					Версия API:
					<select onchange="swagger(this.value)">
						<option value="1">v1</option>
						<option value="2">v2</option>
					</select>
				</label>
			</div>
        </form>
    </div>
</div>

<div id="message-bar" class="swagger-ui-wrap">
    &nbsp;
</div>

<div id="swagger-ui-container" class="swagger-ui-wrap">

</div>

</body>

</html>