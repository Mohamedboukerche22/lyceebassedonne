// Show/hide role-specific fields in registration form
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    document.querySelectorAll('.role-fields').forEach(field => {
        field.style.display = 'none';
    });
    
    if (role === 'student') {
        document.getElementById('studentFields').style.display = 'block';
    } else if (role === 'teacher') {
        document.getElementById('teacherFields').style.display = 'block';
    }
});

// Toggle dark/light mode
const darkModeToggle = document.createElement('div');
darkModeToggle.innerHTML = `
    <button id="themeToggle" class="btn small" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        Toggle Theme
    </button>
`;
document.body.appendChild(darkModeToggle);

document.getElementById('themeToggle').addEventListener('click', function() {
    document.body.classList.toggle('light-mode');
    document.body.classList.toggle('dark-mode');
    
    // Save preference to localStorage
    const isDarkMode = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDarkMode);
});

// Check for saved theme preference
if (localStorage.getItem('darkMode') === 'false') {
    document.body.classList.remove('dark-mode');
    document.body.classList.add('light-mode');
}

// Form validation
document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
        return false;
    }
});

// Add animations
document.addEventListener('DOMContentLoaded', function() {
    // Fade in effect for all elements
    const elements = document.querySelectorAll('.container > *');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 100 * index);
    });
    
    // Hover effects for buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.3)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Table row hover effects
    const tableRows = document.querySelectorAll('table tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
// Notification system
function showNotification(message, type = 'success', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, duration);
}

// Example usage:
// showNotification('Login successful!', 'success');
// showNotification('Invalid credentials!', 'error');
const passwordInput = document.getElementById('password');
if (passwordInput) {
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'password-strength';
    strengthIndicator.style.marginTop = '5px';
    strengthIndicator.style.height = '5px';
    strengthIndicator.style.borderRadius = '3px';
    strengthIndicator.style.transition = 'width 0.3s ease, background-color 0.3s ease';
    strengthIndicator.style.width = '0%';
    passwordInput.parentNode.appendChild(strengthIndicator);
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        if (password.length > 7) strength += 1;
        if (password.length > 11) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
 
        const width = (strength / 5) * 100;
        strengthIndicator.style.width = `${width}%`;
        
        if (strength <= 2) {
            strengthIndicator.style.backgroundColor = 'var(--danger-color)';
        } else if (strength <= 4) {
            strengthIndicator.style.backgroundColor = 'var(--warning-color)';
        } else {
            strengthIndicator.style.backgroundColor = 'var(--success-color)';
        }
    });
}

// Floating animation for feature cards
document.querySelectorAll('.feature-card').forEach((card, index) => {
    card.style.animationDelay = `${index * 0.1}s`;
    card.classList.add('floating');
});