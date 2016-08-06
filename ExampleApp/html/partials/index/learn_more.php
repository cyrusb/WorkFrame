<h1>Thanks for looking at WorkFrame!</h1>
<div class="toc">
	<ol><li><a href="#introduction">Introduction</a></li>
		<li><a href="#motivation">Motivation</a></li>
		<li><a href="#quick-start">Quick start</a></li>
		<li><a href="#basic-features">Features</a></li>
		<li><a href="#ideas">Ideas / future</a></li>
		<li><a href="#shortfalls">Shortfalls</a></li>
	</ol>
</div>

<p>..... More will come</p>


<h2>Introduction <span id="introduction"></span><a href="#introduction" class="hashlink">&para;</a></h2>
<p>This is a first attempt at a super lightweight PHP7 compatible framework that exists to achieve the very basic standard requirements of a framework with (hopefully) a couple of useful unexpected extras.</p>

<h2>Motivation <span id="motivation"></span><a href="#motivation" class="hashlink">&para;</a></h2>
<p>As someone who has worked with frameworks for some years, I've become familiar with the day to day features of frameworks that I (at least) rely on. I've also become familiar with the annoyance that comes when advanced frameworks get abandoned and defunct, or changed so much they basically warrant an application rewrite in order to upgrade the framework.</p>
<p>Since we aren't always able or willing to continue supporting a server running an out of date architecture, I've decided I want a flexible framework that will be basic enough to upgrade to future versions of PHP with little pain, yet powerful enough to provide the primary features we always required.</p>
<p>I've also realised it's often better to not constrain yourself to your framework too much! I've fallen into the trap of following the restrictions of framework design to their entirety in situations where it doesn't necessarily make sense to do so. All that happens is your code becomes so interwoven with the framework that you become completely reliant on it.</p><p>The answer is to create your application in a structure designed for itself, but making use of a framework that handles URI routing, view file mapping, basic ORM and a little bit more. Anything more complex is likely to have bespoke requirements that the framework does not attempt to provide.</p>

<hr/>
<h2>Quick start <span id="quick-start"></span><a href="#quick-start" class="hashlink">&para;</a></h2>
<p>Do the following:</p>
<ol>
	<li><a title="Download this" href="http://tombrown.xyz/downloads/workframe.zip">Download this</a> or <a href="#">pull it from github</a></li>
	<li>Understand what is included (3 directories)
		<ol>The <em>App</em> directory - This will contain your custom application code (which is this site by default)</ol>
		<ol>The <em>WorkFrame</em> directory - This is the framework</ol>
		<ol>The <em>www</em> directory - This is your web root (often called htdocs, public_html, etc)</ol>
	</li>
	<br/>
	<p>You most likely want to keep your <em>App</em> and <em>WorkFrame</em> outside the web root - but it's your server so do as you please.</p>
	<li>(Assuming you run apache and have a vhost ready,) add the following to your vhost config (to route all requests to the framework entry script.)
		<br/>
		<pre>
	&lt;Directory /var/www/yourapp/www&gt;
	  RewriteEngine on
	  RewriteBase /var/www/yourapp/www
	  RewriteCond $1 !^(index\.php|public|robots\.txt)
	  RewriteRule ^(.*)$ /index.php/$1 [L]
	&lt;/Directory&gt;</pre>
	</li>
	<li>Update the definitions in entry script (in ./wwww/index.php) which are hopefully self explanatory.</li>
	<li>Maybe check out the <a href="#basic-features" title="Read about the basic features">features</a></li>
</ol>

<hr/>
<h2>Basic features <span id="basic-features"></span><a href="#basic-features" class="hashlink">&para;</a></h2>
<p>Your app code will essentially follow the pattern of an HMVC framework, but by no means needs to be used as such. Your app will probably at least contain the following:</p>
<h3>Standard constructs</h3>
<dl class="clearfix">
	<dt>Request_handlers <small>(a bit like a controllers)</small></dt>
	<dd>You write your own by extending <em>\WorkFrame\Request_handler</em></dd>
	<dd>These purely exist as landing points for HTTP requests</dd>
	<dd>Requests are mapped to these handlers based on URL segments, with the last being the action name (method name) (E.g. /directoryname/request_handler_name/method_name?get_vars_here</dd>
	<dd>If not provided, index is taken as the default values for both method names and Request_handler names</dd>
	<dd>Note, feel free to override pre_action_hook and post_action_hook, you can probably guess when they get called</dd>
	<br/>
	<dt>Model layer</dt>
	<dd>Handles "business logic" including data manipulation and data storage</dd>
	<dd>Request_handlers (usually) set these up and invoke procedures which retain data to be passed through to the view layer</dd>
	<br/>
	<dt>View layer <small>(html templates and partials)</small></dt>
	<dd>Templates hold standard layouts (html documents,) and (may) include HTML from partials</dd>
</dl>
<p>An application with any degree of complexity will probably benefit from having it's own class structure which do not need to be one of these standard constructs.</p>

<h3>Model layer</h3>
<p>These consist of 3 classes of thing</p>
	<dt>Services</dt>
	<dd>You write your own by extending <em>\WorkFrame\Service</em></dd>
	<dd>These contain the bulk of the business logic</dd>
	<dd>They are responsible for (probably all) manipulation Domain_object's and also handle data persistance (through Data_mappers)</dd>
	<dd>They would usually finish up by holding or returning output related data (presentation data)</dd>
	<br/>
	<dt>Domain_objects</dt>
	<dd>You write your own by extending <em>\WorkFrame\Domain_object</em></dd>
	<dd>They can represent any kind of entity you like, including web forms &amp;/or data for persistance (sometimes 1:1 with DB tables)</dd>
	<dd>They can optionally exhibit <strong>scenarios</strong>, which you define as the programmer to describe the different roles &amp;/or rules of the object</dd>
	<dd>Attribute lists can be defined for each scenario to dictate which attributes can be "mass assigned" (E.g. from a _POST or data mapper)</dd>
	<br/>
	<dt>Data_mapper</dt>
	<dd>You write your own by extending <em>\WorkFrame\Data_mapper</em></dd>
	<dd>... or more likely one of it's children <em>like \WorkFrame\Database_data_mapper</em></dd>
	<dd>This is the persistance end of the model layer</dd>
	<dd>They should accept and return Domain_objects (individually or as lists where appropriate)</dd>
	<dd>This is where you should put DB queries for DB CRUD.</dd>
	<dd>These should not contain (hardly any!) logic.. keep them simple. Their most complex feature may be to apply a condition to a data select</dd>
	<dd>You may want to implement this instead with an ORM library, or perhaps have it just implement a DB access helper library (ActiveRecord, data table gateway etc)</dd>
</dl>


<h3>Loader</h3>
<p>You can load variable components (libraries, services, data_mappers and domain objects) by calling:<br><em>$this->SERVICE($component_classname), $this->DATA_MAPPER($component_classname), etc</em></p>
<p>You can pass an identifer as a second parameter if you want it to be available as if it were a local variable.</p>
<p>To unload an existing instance call:<br/><em>$this->UNLOAD($component_type, $component_name)</em></p>


<h3>The Renderer_trait</h3>

<p>Whilst it isn't written in stone, ou will most likely invoke templates and partials from classes which have this trait (of which Request_handlers are all examples)</p>
<p>Things using this trait can make use of it's add_script<i>s</i>() and add_stylesheet<i>s</i>() methods to conveniently append JS/stylesheet tags to a template
	<br/><small>(Note: There is a built in tool to minify such client side code)</small>
</p>
<p>View data can be passed to the templates and partials with the add_view_vars() method.</p>



<h3>The Processor_trait</h3>

<p>Processors can be attached to fields and can manipulate and/or validate data. The framework comes with some but you can add your own. There is a mechanism for them to work client side (with JavaScript), if the processor has been coded as such</p>
<p>Processors (/validation rules) can be attached to a subset to fields as well as a subset of scenarios on the entity (if the trait is used on a Domain_object (or infact anything with a scenario attribute)).</p>

<h3>\WorkFrame\Html\Form_tools</h3>
<p>To help you write your HTML. These can be instantiated and entities. They return HTML for common things (like form fields and validation errors.)</p>
<p>They also (like most things) be extended if you want extra functionality.</p>

<h3>\WorkFrame\Libraries\Session</h3>
<p>A simple session library is provided. You may use it's following static methods.</p>
<ul>
	<li>write($key, $value)</li>
	<li>read($key, $child=FALSE)</li>
	<li>delete($key)</li>
	<li>dump() <i>[echos current session data array]</i></li>
	<li>regenerate_session_id()</li>
	<li>destroy()</li>
	<li>... if you need more, it might have what you need - check it out.</li>
</ul>

<h3>Application class (\App\App), hooks, etc</h3>
<p>Your app will probably want one of these sitting in it's route. It must have the same classname (and filename) as your application namespace. It must extend \WorkFrame\WorkFrame.</p>
<p>There are some standard hooks (like pre router, pre action etc) which can be defined in this class. You can also use this class to do anything application wide (like store current user, etc.)</p>
<p></p>

<br/>

<h3>Logging</h3>
<p>There is a very simple logging mechanism which is basically a single function: <em>log_message($level, $message)</em>. It writes to a logs directory in your App.</p>

<h3>Conf</h3>
<p>This is a simple mecahnism to define and retrieve config from files in the Conf directory.</p>
<p>Simple call conf($conf_filename) to call and return data from the conf with that $conf_filename.</p>
<p>If you want to use the existing mysql DB support, you must define (1 or more) DB connections in db.php (see example file.)</p>
<hr/>


<h2>Ideas / future <span id="ideas"></span><a href="#ideas" class="hashlink">&para;</a></h2>
<p>Here is what I hope to work on (in chronological order)</p>
<ol>
	<li>User type based auth rules / config</li>
	<li>A built in DB accessor system (for use in your Data_mapper's)</li>
	<li>Simple RESTful API support</li>
	<li>Built in XSS filtering</li>
	<li>A cookie library</li>
	<li>.. anything else fundamental missing? Please <a title="Email me" href="mailto:tombrown86@gmail.com">let me know</a></li>
</ol>

<hr/>

<h2>Shortfalls <span id="shortfalls"></span><a href="#shortfalls" class="hashlink">&para;</a></h2>
<p>I'm less likely to work on some features which more complete frameworks can offer</p>
<ol>
	<li>Currently, built in DB access is limited to mysqli</li>
	<li>Some of the front end features rely on the availability of JavaScript</li>
	<li>Will not work on windows! (basically due to directory separators!)</li>
	<li>No support for internationalisation</li>
	<li>No built in benchmarking</li>
	<li>No testing suite. Infact, the framework itself isn't unit tested.. 
<br/><small>Unit tests could probably be written for your application components without problem. There is potentially an issue with anything loaded through the WorkFrame Loader ($this->LOAD) since it loads components using some magic (and not using dependency injection.) I think this issue can be worked around by overwriting the loader with your own fake test loader (you can inject it with set_loader, a func which should be available on all components.)</small></li>
	<li>No caching</li>
	<li>... it could go on for a while</li>
</ol>
<p>However, I wrote this framework purely to provide the fundamentals. You can pull in whatever functionality you want.</p>
