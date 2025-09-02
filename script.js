// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
    }));
    
    // Category filtering
    const filterButtons = document.querySelectorAll('.filter-btn');
    const ideaCards = document.querySelectorAll('.idea-card');
    
    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                const filterValue = button.getAttribute('data-filter');
                
                ideaCards.forEach(card => {
                    if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }
    
    // Voting functionality
    const voteButtons = document.querySelectorAll('.vote-btn');
    
    voteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const ideaId = this.getAttribute('data-id');
            const isVoted = this.classList.contains('voted');
            
            // Send AJAX request to vote/unvote
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'vote.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    
                    if (response.status === 'success') {
                        const voteCount = button.querySelector('.vote-count');
                        
                        if (isVoted) {
                            button.classList.remove('voted');
                            voteCount.textContent = parseInt(voteCount.textContent) - 1;
                        } else {
                            button.classList.add('voted');
                            voteCount.textContent = parseInt(voteCount.textContent) + 1;
                        }
                    } else {
                        alert(response.message);
                    }
                }
            };
            
            xhr.send(`idea_id=${ideaId}&action=${isVoted ? 'unvote' : 'vote'}`);
        });
    });
});
