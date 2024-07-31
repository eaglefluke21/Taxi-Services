import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Header from '../components/Header';
import Footer from '../components/Footer';


const NotAuthorized = () => {
  const navigate = useNavigate();

  // Redirect to home page after 5 seconds
  useEffect(() => {
    const timeoutId = setTimeout(() => {
      navigate('/');
    }, 3000); // Adjust the delay time as needed (in milliseconds)

    // Clean up the timeout
    return () => clearTimeout(timeoutId);
  }, [navigate]);

  return (
    <div className="flex flex-col min-h-screen  bg-red-400 ">

    <Header/>


    <div className=' flex flex-col gap-4 justify-center items-center flex-grow  '>
<h1 className='text-white font-quick  sm:text-3xl text-xl font-bold'>Not Authorized</h1>
<p className='text-white font-quick text-xl'> User Only Page</p>
</div>

    <Footer/>


</div>
  );
};

export default NotAuthorized;