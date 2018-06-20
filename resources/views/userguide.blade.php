@extends('layouts.app')

@section('content')

@include('partials.sidebar')

<div class="container">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">User Guide</h3>
		</div>
		<div class="card-body" data-spy="scroll" data-target=".navbar" data-offset="50">
			<h5 class="card-text" style="color:black;">Using Evidencio's <b>Designer</b>, you can integrate your Evidencio Models to Evidencio Patient Portal. These integrated models are called <b>Workflows</b>.</h5>

			<br>

			<div class="card" style="width: 25rem;">
				<div class="card-body">
			<h5><b>Content</b></h5>
  				<ol>
   					 <li><a href="#section1"><h5 class="card-text" style="color:blue;">Add a Title and a Description</h5></a></li>
   					 <li><a href="#section2"><h5 class="card-text" style="color:blue;">Find your Evidencio Model</h5></a></li> 
   					 <li><a href="#section3"><h5 class="card-text" style="color:blue;">The Variables</h5></a></li>
   					 <li><a href="#section4"><h5 class="card-text" style="color:blue;">Add Levels and Steps</h5></a></li>
   					 <li>
   					 	<a href="#section5"><h5 class="card-text" style="color:blue;">Modify the Step</h5></a>
   					 	<ol>
   					 		<li><a href="#section7"><h5 class="card-text" style="color:blue;">Style Your Step</h5></a></li>
   					 		<li><a href="#section8"><h5 class="card-text" style="color:blue;">Variables Menu</h5></a></li>
   					 		<li><a href="#section9"><h5 class="card-text" style="color:blue;">Model Calculation Menu</h5></a></li>
   					 		<li><a href="#section10"><h5 class="card-text" style="color:blue;">Logic Menu</h5></a></li>
   					 	</ol>	
   					 </li>
					 <li><a href="#section11"><h5 class="card-text" style="color:blue;">Add Levels and Steps</h5></a></li>
   					 <li><a href="#section6"><h5 class="card-text" style="color:blue;">Save your Workflow</h5></a></li>
					 <li><a href="#section12"><h5 class="card-text" style="color:blue;">Example of a workflow design cycle</h5></a></li>
   				</ol>
		</div>
	</div>
			<br>

			<ol>
				<div id="section1">
					<li>
						<h5><b>Add a Title and a Description</b></h5>
						<p class="card-text" style="color:black;">To add a <b>Title</b> and a <b>Description</b> to your workflow, you can use the top right menu. Click on   <img  src="/images/pencil.svg" alt="Edit"  style="width:16px;height:16px;"> to edit and click on <img src="/images/check.svg" alt="Edit" class="buttonIcon right" style="width:16px;height:16px;"> to save. </p>
						<p style="text-align: center;"> <img src="{{ URL::to('/images/instructions_1.JPG') }}" ></p>
					</li>
				<div>

				<br>

				<div id="section2">
					<li>
						<h5><b>Find your Evidencio Model</b></h5>
						<p class="card-text" style="color:black;">You can search for your Evidencio model using the bottom left menu. Here, enter a keyword and click on <button type="button" class="btn btn-primary btn-sm">Search Evidencio Model</button>. You will see a list of Evidencio models. You can pick your model from this list. </p>
						<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_2.JPG') }}"></p>
					</li>
				</div>

				<br>

				<div id="section3">
					<li>
						<h5><b>The Variables</b></h5>
						<p class="card-text" style="color:black;">Once you pick the model, the <b>Variables</b> that used to calculate the medical prediction result for that models will be loaded inside the menu. You can click on the variables to see their attributes such as the range and unit. Near the variables, you will notice a red bubble. The number inside the bubble shows how many times that variable is used. </p>
						<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_3a.JPG') }}">
						<img src="{{ URL::to('/images/instructions_3b.JPG') }}"></p>
					</li>
				</div>

				<br>

				<div id="section4">
					<li>
						<h5><b>Add Levels and Steps</b></h5>
						<p class="card-text" style="color:black;">The right side of the page is the tool where you can add <b>Levels and Steps</b> You can click the blue button to add a <b>Level</b> to your workflow and click the green button to add a <b>Step</b> to your level. Each blue rectangle represents a <b>Step</b> in your workflow.</p>
						<p class="card-text" style="color:black;">Your workflow may be too big to fit into the screen. To display the whole workflow, click on <button type="button" class="btn btn-primary btn-sm">Fit</button></p>
						<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_4.JPG') }}" height="341" width="600"></p>
					</li>
				</div>

				<br>

				<div id="section5">
					<li>
						<h5><b>Modify the Step</b></h5>
						<p class="card-text" style="color:black;"> You can click on the steps to modify them using the <b>Step Options</b> menu. You can style your step using the top part of the menu. The bottom part of the menu has three parts: <b>Variables, Model calculation and Logic.</b> where you can define the functionality of the step.</p>
						<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_4_5.JPG') }}" width="518" height="550"></p>
						<ol>
							<div id="section7">
								<li>
									<h6><b>Style Your Step</b></h6>
									<ul>
										<li>
											<p class="card-text" style="color:black;">From the top part of the menu, you can pick a color for each <b>Step</b> to visualize your workflow.</p>
										</li>
										<li>
											<p class="card-text" style="color:black;">To define the <b>Step</b> you can add a title and a description. Click on   <img  src="/images/pencil.svg" alt="Edit"  style="width:16px;height:16px;"> to edit and click on <img src="/images/check.svg" alt="Edit" class="buttonIcon right" style="width:16px;height:16px;"> to save.</p>
										</li>
										<li>
											<p class="card-text" style="color:black;">It is extremely important to define the type of your <b>Step</b> (whether it is an <b>Input Step</b> or a <b>Result Step</b>).</p>
										</li>
									</ul>
									<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_5.JPG') }}"></p>
								</li>
							</div>
						
						<br>

							<div id="section8">
								<li>
									<h6><b>Variables Menu</b></h6>
									<p class="card-text" style="color:black;">You can pick the variables to be used in this <b>Step</b> using the <b>Variables Menu</b>.</p>
									<ul>
										<li>
											<p class="card-text" style="color:black;">Choose the variables you want to add using the dropdown menu. </p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_6a.JPG') }}"></p>
										</li>
										<li>
											<p class="card-text" style="color:black;">You can always discard the variable using the red cross next to it.</p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_6b.JPG') }}"></p>
										</li>
										<li>
											<p class="card-text" style="color:black;">Once you pick your variables, you can edit their title and description way by clicking on them. Click on   <img  src="/images/pencil.svg" alt="Edit"  style="width:16px;height:16px;"> to edit and click on <img src="/images/check.svg" alt="Edit" class="buttonIcon right" style="width:16px;height:16px;"> to save. </p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_6c.JPG') }}"></p>
										</li>
									</ul>
								</li>
							</div>

						<br>

							<div id="section9">
								<li>
									<h6><b>Model Calculation Menu</b></h6>
									<p class="card-text" style="color:black;">When you are done with your variables, you can match your variables and Evidencio variables for result calculation of that Step using the <b>Model calculation</b> menu.</p>
									<ul>
										<li>
											<p class="card-text" style="color:black;">Choose the model you want to use using the dropdown menu. </p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_7a.JPG') }}"></p>
										</li>
										<li>
											<p class="card-text" style="color:black;">You can always discard model using the red cross next to it.</p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_7b.JPG') }}"></p>
										</li>
										<li>
											<p class="card-text" style="color:black;">Match your new variables with your Evidencio Model variables.</p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_7c.JPG') }}"></p>
										</li>
									</ul>
								</li>
							</div>

						<br>

							<div id="section10">
								<li>
									<h6><b>Logic Menu</b></h6>
									<p class="card-text" style="color:black;">In your<b>Workflow</b>, you will probably want to connect your <b>Steps</b> using <b>Rules</b>. This is what the <b>Logic Menu</b> is for.</p>
									<p class="card-text" style="color:black;"><b>The Rules</b> are used to connect steps. You can make a new rule either with or without a condition.</p> 
									<p class="card-text" style="color:black;">A rule with a condition can be used to select a next step based on the previously calculated results. This can be done with a single comparison or with multiple comparisons combined using logical AND and OR. </p> 
									<ul>
										<li>
											<p class="card-text" style="color:black;">Click on <button type="button" class="btn btn-primary btn-sm">Add Rule</button> to add a new rule.</p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_8a.JPG') }}"></p>
										</li>
										<li>
											<p class="card-text" style="color:black;">Click on the new rule you create and you will see a new menu. Here, you can add a condition for your rule. Click on   <img  src="/images/pencil.svg" alt="Edit"  style="width:16px;height:16px;"> to edit and click on <img src="/images/check.svg" alt="Edit" class="buttonIcon right" style="width:16px;height:16px;"> to save. </p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_8b.JPG') }}"></p>
										</li>
										<li>
											<p class="card-text" style="color:black;">You can specify a custom option template clicking on the blue box. </p>
											<p style="text-align: center;"><img src="{{ URL::to('/images/instructions_8c.JPG') }}"></p>
										</li>
									</ul>
								</li>
							</div>
						</ol>

					<br>

					<p class="card-text" style="color:black;">When you are done modifying your step, click on <button type="button"class="btn btn-danger btn-sm">Remove</button> to remove to step, click on <button type="button" class="btn btn-primary btn-sm">Apply</button> to apply your changes or click on <button type="button" class="btn btn-secondary btn-sm">Cancel</button> to cancel. </p>
					
					</li>
				</div>

				<br>

				<div id="section11">
					<li>
						<h5><b>Output Editor</b></h5>
						<p class="card-text" style="color:black;"> You can also edit the output of the workflow, as
							described in the last section of this tutorial. It's layout is as follows:</p>
						<p style="text-align: center;"><img width="50%" height="50%" src="{{ URL::to('/images/EntireModal.png') }}"></p>
					</li>
				</div>

				<br>

				<div id="section6">
					<li>
						<h5><b>Save your Workflow</b></h5>
						<p class="card-text" style="color:black;">Evidencio Patient Portal does not autosave your changes. Do not forget to click on <button type="button" class="btn btn-primary btn-sm">Save Workflow</button> to save your workflow. </p> 
					</li>
				</div>

				<br>

				<div id="section12">
					<li>
						<h5><b>Example of a workflow design cycle</b></h5>
						<p class="card-text" style="color:black;">For this quick tutorial on how to use the function of
							customizing steps, we will try to go over a process of making a mock workflow. This workflow
							will only consist of an input step and an result step.
						</p>
						<p class="card-text" style="color:black;">Firstly, we need to make two steps. We do so by using
							the &lsquo;+&rsquo; buttons. We will click the blue plus button on the level below the
							(already existing) root node.<br />Furthermore, a model needs to be loaded. You can do so by
							clicking on a &ldquo;Search Evidencio Model&rdquo; button and searching for the adequate
							model. This will tell us precisely which variables need to be used for the calculation of
							the given model. This is done through the &lsquo;variables&rsquo; panel in the lower left
							corner. You can also keep track of the number of times you have used a variable (hint: if
							it wasn&rsquo;t used the number next to it will be a zero in a red circle).
						</p>
						<p class="card-text" style="color:black;">It is advisable to keep everything adequately named
							and patient friendly.
						</p>
						<p class="card-text" style="color:black;">Secondly, we go to the input step (in our case the
							root node), and under the section &lsquo;variables&rsquo; select all of the variables which
							are going to asked for in this step. Also these are the variables which are needed for the
							calculation of the model. Every variable can be edited by clicking on it.<br />When all is
							patient friendly and filled in, you can advance to the section &lsquo;model
							calculation&rsquo; in which you will define the corresponding model which uses the variables
							provided in the calculation. This model, after the calculation, returns a return variable
							which is named in the lines of: &ldquo;result_0123_0&rdquo; (where the numbers will probably
							differ)<br />After selecting the variables and the model, you should advance to the section
							&lsquo;logic&rsquo;. This is the key to connecting steps. Here, you will, based on the return
							variable(s) (previously explained), calculate the desirable next step(s). We will select the
							previously added step as the target node and will use the blank rule. Note that you can use
							any of the given operations to compare the result variable.<br />If done properly, after
							clicking save, a line should appear between these two steps. It marks a connection between
							them.
						</p>
						<p class="card-text" style="color:black;">Thirdly, since we want to communicate the result with
							the patient, we will click on the step below and in the top-left part select from the
							drop-down menu the result step. This will change the layout.<br />Now, it is worth mentioning
							that the result steps can only be utilized if somewhere in the ancestor steps (steps above a
							certain step, which are connected to it directly or via other steps), there is a loaded model
							which returns a return variable. Since we have done this, we can continue.<br />The main part
							of the window is split into two parts. Left is where you will edit the data of the output,
							and on the right you will be able to see how it would be communicated to the user.<br />We
							begin by clicking the button &ldquo;Add item&rdquo;. This will generate a new item in the
							chart. Moreover, it will show a new drop-down menu on the right where you can edit the data
							of the corresponding chart item. In this drop-down menu, you will see tools to edit the
							label, color, value, result variable, and a checkbox to toggle the negation mode.
						</p>
						<ul>
							<li style="line-height: normal; tab-stops: list 36.0pt;"><p class="card-text" style="color:black;">label
									is the short description of an associated chart item</p>
							</li>
							<li style="line-height: normal; tab-stops: list 36.0pt;"><p class="card-text" style="color:black;">color
									is used to further describe the chart item</p>
							</li>
							<li style="line-height: normal; tab-stops: list 36.0pt;"><p class="card-text" style="color:black;">value
									is used only by you in order to see how the graph looks like in different scenarios</p>
							</li>
							<li style="line-height: normal; tab-stops: list 36.0pt;"><p class="card-text" style="color:black;">result
									variable is used to connect a chart item to a calculation result obtained in one of
									the input steps above the given node.</p>
							</li>
							<li style="line-height: normal; tab-stops: list 36.0pt;"><p class="card-text" style="color:black;">The
									negation box is used in the scenario where, for example, you want to show the user
									the percentage. You would make two chart items for the same variable. One would show
									the percentages as the model calculates them (without the checkbox checked), while
									the other, which has the checkbox checked, would communicate to the patient the other,
									complement, option (rest of the percentages). This is calculated by a simple 100-the
									result. It is provided as a tool so that you can make both results patient
									friendly.</p>
							</li>
						</ul>
						<p class="card-text" style="color:black;">After this has been done you can save the step. Now,
							if you are satisfied with it, click the &lsquo;Publish&rsquo; button in the upper-right area.
							This will send the model to our team who will review it and either decide to ask you to correct
							it a bit or open it for the public. In case of the former, You can find it in your drafts,
							under the section &ldquo;My workflows&rdquo; found in the sidebar. And, in case of the latter,
							we congratulate you because your model is ready for the patients and doctors to use.</span>
						</p>
						<p class="card-text" style="color:black;">&nbsp;</p>
						<p class="card-text" style="color:black;">Good luck and kind regards,</p>
						<p class="card-text" style="color:black;">The Evidencio Patient Portal Team</p>
					</li>
				</div>
			
			</ol>

		</div>
	</div>
</div>


@endsection