function Footer () {

    return (
        <>

        <footer className="bg-white   border-t-4 border-gray-100  mt-auto  flex  justify-center py-2  ">

        <ul className="grid grid-cols-2 md:grid-cols-4  text-md sm:text-xl  mt-3  font-anta text-black sm:gap-y-0 gap-y-2 ">
        <li>
            <a href="#" className="hover:underline   me-4 sm:me-12">About</a>
        </li>
        <li>
            <a href="#" className="hover:underline  me-4 sm:me-12">Contact</a>
        </li>
        <li>
            <a href="#" className="hover:underline  me-4 sm:me-12">Licensing</a>
        </li>
        <li>
            <a href="#" className="hover:underline  ">Privacy Policy</a>
        </li>
    </ul>


        </footer>
        
        </>
    )

}

export default Footer;
