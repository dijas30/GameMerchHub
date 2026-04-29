/* ==========================================
   MOBILE MENU TOGGLE
   ========================================== */


const menuIcon = document.getElementById("menu-icon");


const navMenu = document.getElementById("menu");


if (menuIcon) 
{
  menuIcon.addEventListener("click", function() 
  {
    if (navMenu.className == "hidden") 
    {
      navMenu.classList.remove("hidden");
    } 
    else 
    {
      navMenu.classList.add("hidden");
    }
  });
}


/* ==========================================
   FILTER CATEGORY FUNCTION
   ========================================== */

function filterCategory(category) 
{
  const sections = document.querySelectorAll(".category-section");
  
  const buttons = document.querySelectorAll(".filter-btn");

  buttons.forEach(function(btn) 
  {
    btn.classList.remove("active");
  });
  

  event.target.classList.add("active");
  
  if (category === "all") 
  {
    sections.forEach(function(section) 
    {
      section.classList.remove("hidden");
    });
    
  } 
  else 
  {
    sections.forEach(function(section) 
    {
      if (section.getAttribute("data-category") === category) 
      {
        section.classList.remove("hidden");
      } 
      // নাহলে (মিলে না)
      else 
      {
        section.classList.add("hidden");
      }  
    });
    
  }
  
}


/* ==========================================
   SEARCH PRODUCTS FUNCTION
   ========================================== */

function searchProducts() 
{
  const searchInput = document.getElementById("searchInput");
  const searchValue = searchInput.value.toLowerCase();
  
  const categorySections = document.querySelectorAll(".category-section");
  
  categorySections.forEach(function(categorySection) 
  {
    const productBoxes = categorySection.querySelectorAll(".product-box");
    
    let visibleProductCount = 0;
    
    productBoxes.forEach(function(box) 
    {
      const productNameElement = box.querySelector(".product-name");
      const productName = productNameElement.textContent.toLowerCase();
      
      if (productName.includes(searchValue)) 
      {
        box.style.display = ""; 
        visibleProductCount++; 
      } 
      else 
      {
        box.style.display = "none";
      }   
    });

    if (visibleProductCount === 0) 
    {
      categorySection.classList.add("hidden"); 
    } 

    else 
    {
      categorySection.classList.remove("hidden");
    }   
  });
  
}