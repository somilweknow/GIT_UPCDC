// JavaScript Document

const progress = (value) => {
	let bar = document.getElementsByClassName('progress-bar')[0];
	if (bar) bar.style.width = `${value}%`;
}


var step = document.getElementsByClassName('step');
let prevBtn = document.getElementById('prev-btn');
let nextBtn = document.getElementById('next-btn');
let submitBtn = document.getElementById('submit-btn');
let form = document.getElementsByTagName('form')[0];
let preloader = document.getElementById('preloader-wrapper');
let bodyElement = document.querySelector('body');
let succcessDiv = document.getElementById('success');

form.onsubmit = () => { return false }

var current_step = 0;
var stepCount = step.length - 1;

// In this form, "6. संस्था भवन/सम्पत्ति का विवरण" is the intended last module.
// Sometimes extra `.step` blocks may exist, so detect the final step by heading text.
const getLastFormStepIndex = () => {
	// Prefer explicit marker in HTML (most reliable)
	for (let i = 0; i < step.length; i++) {
		if (step[i]?.getAttribute?.('data-last-step') === '1') {
			return i;
		}
	}

	for (let i = 0; i < step.length; i++) {
		const h4 = step[i]?.querySelector?.('h4');
		const text = (h4?.textContent || '').trim();
		if (text.includes('6.') || text.includes('6 ।') || text.includes('६.')) {
			return i;
		}
	}
	return stepCount;
};

const lastFormStepIndex = getLastFormStepIndex();

const updateStepVisibility = () => {
	// Scroll to top for better UX
	window.scrollTo({ top: 0, behavior: 'smooth' });

	for (let i = 0; i < step.length; i++) {
		if (i === current_step) {
			step[i].classList.remove('d-none');
			step[i].classList.add('d-block');
		} else {
			step[i].classList.remove('d-block');
			step[i].classList.add('d-none');
		}
	}

	// Button Visibility Logic
	if (current_step === 0) {
		prevBtn.classList.remove('d-inline-block');
		prevBtn.classList.add('d-none');

		nextBtn.classList.remove('d-none');
		nextBtn.classList.add('d-inline-block');

		submitBtn.classList.remove('d-inline-block');
		submitBtn.classList.add('d-none');
	} else if (current_step === lastFormStepIndex) {
		prevBtn.classList.remove('d-none');
		prevBtn.classList.add('d-inline-block');

		nextBtn.classList.remove('d-inline-block');
		nextBtn.classList.add('d-none');

		submitBtn.classList.remove('d-none');
		submitBtn.classList.add('d-inline-block');
	} else {
		prevBtn.classList.remove('d-none');
		prevBtn.classList.add('d-inline-block');

		nextBtn.classList.remove('d-none');
		nextBtn.classList.add('d-inline-block');

		submitBtn.classList.remove('d-inline-block');
		submitBtn.classList.add('d-none');
	}

	if (lastFormStepIndex > 0) {
		progress((100 / lastFormStepIndex) * current_step);
	}
};

// Initialize visibility on load
updateStepVisibility();

nextBtn.addEventListener('click', () => {
	if (current_step < lastFormStepIndex) {
		current_step++;
		updateStepVisibility();
	}
});

prevBtn.addEventListener('click', () => {
	if (current_step > 0) {
		current_step--;
		updateStepVisibility();
	}
});

// Optional: Keep success animation if needed, or rely on save_draft()

submitBtn.addEventListener('click', () => {
	preloader.classList.add('d-block');
	const timer = ms => new Promise(res => setTimeout(res, ms));
	timer(1000)
	  .then(() => {
		   bodyElement.classList.add('loaded');
	  }).then(() =>{
		  step[lastFormStepIndex].classList.remove('d-block');
		  step[lastFormStepIndex].classList.add('d-none');
		  nextBtn.classList.remove('d-inline-block');
		  nextBtn.classList.add('d-none');
		  submitBtn.classList.remove('d-inline-block');
		  submitBtn.classList.add('d-none');
		  if(succcessDiv) {
			  succcessDiv.classList.remove('d-none');
			  succcessDiv.classList.add('d-block');
		  }
	  })
});
