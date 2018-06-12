@extends('layouts.app')

@section('content')

@include('partials.sidebar')

<div class="container">
	<div class="card">
		<div class="card-header">
			<h5 class="card-title">About</h5>
		</div>
		<div class="card-body">
			
			<p class="card-text" style="color:black;"><a href="https://www.evidencio.com/" id="logo">Evidencio</a> is an open library that holds quality-controlled medical prediction models and is continuously growing. These prediction models can be used to translate results from clinical studies towards patient-specific probabilities, therewith supporting medical decision-making for individual patients.</p>
			
			<p class="card-text" style="color:black;"><b>Evidencio Patient Platform</b> is a stand-alone portal that utilizes the Evidencio API to integrate models. The purpose of the Platform is to strengthen patient empowerment by providing personalized decision support. It represents Evidencio calculated models to patients in an understandable way. Patients can search for medical prediction models corresponding to their diseases and fill them in with their spesific data to obtain graphical representation of their results. </p>
			
			<p style="color:black;">Now, using the Evidencio Patient Portal patients can have:
				<ul>
					<li>Insight in medical decisions and consequences.</li>
					<li>Control over their own treatment plan.</li>
					<li>The opportunity for shared decision making.</li>
				</ul>	
			</p>
			
			@guest
				<p style="color:black;"><b>Are You a Medical Professional?</b>
				<br><a href="{{ route('register') }}" id="logo">Register now</a> to integrate your Evidencio models to the Evidencio Patient Portal!</p>
			@else
				<p style="color:black;"><b>Are You a Medical Professional?</b><br>Register now to integrate your Evidencio models to the Evidencio Patient Portal</p>
			@endguest
			
		</div>
	</div>
</div>

@endsection








