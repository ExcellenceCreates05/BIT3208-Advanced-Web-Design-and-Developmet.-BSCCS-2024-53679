


const sidebar = document.getElementById('sidebarMenu');
const toggleBtn = document.getElementById('toggleMenuBtn');


toggleBtn.addEventListener('click', function() {
    
   
    sidebar.classList.toggle('hidden');
    
    
    if (sidebar.classList.contains('hidden')) {
        toggleBtn.textContent = "Show Sidebar";
        toggleBtn.style.backgroundColor = "gray"; // Visual feedback
    } else {
        toggleBtn.textContent = "Hide Sidebar";
        toggleBtn.style.backgroundColor = "blue"; 
    }
});