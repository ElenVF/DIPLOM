import './images/circle.svg'
import './images/logo.svg'
import './images/footer.png'
import './images/home-mobile.png'
import './images/footer-mobile.png'
import './images/account-mobile.png'
import './images/events-mobile.png'
import './images/login-mobile.png'
import './images/registration-mobile.png'

document.addEventListener('DOMContentLoaded', (e) => {
    let btn = document.getElementById('header-search-btn')
    btn.onclick = function (el) {
        let search = document.getElementById('header-search')
        console.log(search.style.display)
        if (search.style.display === "block") {
            search.style.display = "none"
            // const bsCollapse = new bootstrap.Collapse(document.querySelector('.navbar-expand-md'), {toggle:false})
        } else {
            search.style.display = "block"
        }
    }
})