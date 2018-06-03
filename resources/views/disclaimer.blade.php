@extends('layouts.app')

@section('content')

<div class="container">
	<div class="card">
		<div class="card-header">
			<h5 class="card-title">Disclaimer</h5>
		</div>
		<div class="card-body">

		<p class="card-text"><b>In short:</b></p>

		<p class="card-text">The information, calculators, equations, and algorithms presented on any of the websites, applications, apps or services provided by Evidencio are provided by Evidencio users (authors). Evidencio assumes no liability for the correctness or applicability of any of the information, calculators, equations, and algorithms presented on any the websites, applications, apps or services provided by Evidencio.</p>

		<p class="card-text">Evidencio cannot and will not be held legally, financially, or medically responsible for decisions made using any information, calculators, equations, and algorithms made available on its websites, applications, apps or services.</p>

		<p class="card-text"><b>Official Legal Disclaimer:</b></p>

		<p class="card-text">Evidencio provides information, models, calculators, equations and algorithms (tools) intended for use by healthcare professionals. These tools do not give professional advice; physicians and other healthcare professionals who use these tools should exercise their own clinical judgment as to the information they provide. Consumers who use the tools do so at their own risk. Individuals with any type of medical condition are specifically cautioned to seek professional medical advice before beginning any sort of health treatment. For medical concerns, including decisions about medications and other treatments, users should always consult their physician or other qualified healthcare professional.</p>

		<p class="card-text">The contents of the Evidencio Site, such as text, graphics and images are for informational purposes only. Evidencio does not recommend or endorse any specific tests, physicians, products, procedures, opinions, or other information that may be mentioned on the Site.</p>

		<p class="card-text">Evidencio explicitly does not warrant the accuracy of the information contained on this site.</p>

		<p class="card-text">Evidencio does not give medical advice, nor do we provide medical or diagnostic services. Medical information changes rapidly. Neither we nor our authors guarantee that the content covers all possible uses, directions, precautions, drug interactions, or adverse effects that may be associated with any therapeutic treatments.</p>

		<p class="card-text">Your reliance upon information and content obtained by you at or through this site is solely at your own risk. Neither we nor our authors assume any liability or responsibility for damage or injury (including death) to you, other persons or property arising from any use of any product, information, idea or instruction contained in the content or services provided to you.</p>

		<p class="card-text"><b>Please note:</b> your use of the websites, applications, apps or services provided by Evidencio is subject to our <a href="{{ route('termsandconditions') }}">Terms & Conditions</a></p>

		<p class="card-text"><i>Last revised: June 9, 2017</i></p>

		</div>
	</div>
</div>

@endsection