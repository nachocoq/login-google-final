const loginCard = document.getElementById('login-card'),
 registerCard = document.getElementById('register-card'),
 showRegisterLink = document.getElementById('show-register'),
 showLoginLink = document.getElementById('show-login');

function change(a, b, c){
	if (a === '1') {
		loginCard.classList.add('active');
		loginCard.classList.remove('disabled');
	} else {
		loginCard.classList.remove('active');
		loginCard.classList.add('disabled');
	}
	
	if (b === '1') {
		registerCard.classList.add('active');
		registerCard.classList.remove('disabled');
	} else {
		registerCard.classList.remove('active');
		registerCard.classList.add('disabled');
	}
	
	registerCard.style.transform = c; 
}

showRegisterLink.addEventListener('click', () => {
	change('0', '1', 'translateY(0)');
});

showLoginLink.addEventListener('click', () => {
	change('1', '0', 'translateY(0)');
});