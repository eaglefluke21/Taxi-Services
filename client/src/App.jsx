import { BrowserRouter as  Router, Routes, Route} from "react-router-dom";
import Home from "./pages/Home";
import Book from "./pages/Book";
import Login from "./pages/Login";
import Signup from "./pages/Signup"
import DriverSignup from "./pages/DriverSignup";
import Services from "./pages/Services";
import NotAuthorized from "./utils/NotAuthorized";
import userRole from "./utils/userRole";
import Status from "./pages/Status";


const RoleBase = userRole(Book, [ 'user']);


const App = () => {

  return (
<>
<Router>
  <Routes>

<Route path="/" element={<Home/>}/>
<Route path="/Services" element={<Services/>}/>
<Route path="/Login" element={<Login/>}/>
<Route path="/Signup" element={<Signup/>}/>
<Route path="/DriverSignup" element={<DriverSignup/>}/>
<Route path="/Book" element={<RoleBase/>}/>
<Route path="/noAuth" element={<NotAuthorized />} />
<Route path="/status" element={<Status/>}/>


  </Routes>
</Router>
</>    
  )
}


export default App;