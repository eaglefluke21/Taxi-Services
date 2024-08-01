import React from "react";
import { NavLink } from "react-router-dom";
import { useState } from "react";
import close from "../assets/close.svg"
import { useEffect } from "react";

const NavLinks = () => {

    const [isLoggedIn, setIsLoggedIn] = useState(false); // Initial login state

    useEffect(() => {
      const jwtToken = sessionStorage.getItem('jwToken');
      setIsLoggedIn(!!jwtToken); 
    }, []);
  
    const handleLogout = () => {
      sessionStorage.removeItem('jwToken');
      setIsLoggedIn(false);
    };


    return (
       
<>
<NavLink to="/" className="  sm:text-2xl font-quick  w-full text-center lg:w-auto hover:bg-gray-200 lg:hover:bg-transparent  font-semibold "> Home</NavLink>
<NavLink to="/Services" className=" sm:text-2xl font-quick  w-full text-center lg:w-auto hover:bg-gray-200 lg:hover:bg-transparent font-semibold "> Services </NavLink>
<NavLink to="/Status" className=" sm:text-2xl font-quick  w-full text-center lg:w-auto hover:bg-gray-200 lg:hover:bg-transparent font-semibold "> Status </NavLink>
<NavLink to="/Login" className="sm:text-2xl font-quick  w-full text-center lg:w-auto hover:bg-gray-200 lg:hover:bg-transparent font-semibold "> {isLoggedIn ? (<span onClick={handleLogout} > Logout </span>) : (<span > Log In</span>)  }  </NavLink>
<NavLink to="/Book" className="lg:ml-auto lg:pr-8"> <button  className="bg-black sm:py-2 sm:px-3 py-1 px-8 rounded-md text-xl font-quick font-semibold text-amber-300"> Book Now </button>  </NavLink>

</>
    )

};

const Nav = () => {
    const [isOpen, SetIsOpen] = useState(false);

    const toggleNavbar = () => {
        SetIsOpen(!isOpen);
    };


    return (
        <>
            <nav className="">
                <div className="hidden lg:flex  gap-x-10 ">
                    <NavLinks  />
                </div>

                <div className="lg:hidden">
                    <button onClick={toggleNavbar} className="pr-8">
                        {isOpen ? <span className="   text-3xl font-quick text-black" > <img src={close} className="h-8 w-8" /> </span> : <span className="bg-black py-2 px-3 rounded-lg text-sm font-comic text-white "> Menu</span>}
                    </button>
                </div>

            </nav>

            {isOpen && (
                <div className="flex flex-col basis-full items-center gap-2 shadow-md py-4 lg:hidden">
                    <NavLinks />
                </div>
            )}

        </>
    )
};

export default Nav;



