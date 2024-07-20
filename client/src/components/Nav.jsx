import { NavLink } from "react-router-dom";
import { useState } from "react";
import close from "../assets/close.svg"


const NavLinks = () => {

  

    return (
        <>
            <NavLink to="/" className="  sm:text-2xl font-quick  w-full text-center lg:w-auto hover:bg-gray-200 lg:hover:bg-transparent  font-semibold "> Home</NavLink>
            <NavLink to="/Services" className=" sm:text-2xl font-quick  w-full text-center lg:w-auto hover:bg-gray-200 lg:hover:bg-transparent font-semibold "> Services </NavLink>
            <NavLink to="/Login" className=" sm:text-2xl font-quick  w-full text-center lg:w-auto hover:bg-gray-200 lg:hover:bg-transparent font-semibold "> Login </NavLink>
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
                        {isOpen ? <span className="   text-3xl font-quick text-black" > <img src={close} className="h-8 w-8" /> </span> : <span className="bg-black py-2 px-3 rounded-lg text-sm font-comic font-medium  text-amber-300 "> Menu</span>}
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