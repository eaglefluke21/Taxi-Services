import Home from "./pages/Home";
import Book from "./pages/Book";
import Login from "./pages/Login";
import Signup from "./pages/Signup"
import Services from "./pages/Services";
import { BrowserRouter as  Router, Routes, Route} from "react-router-dom";

const App = () => {

  return (
<>
<Router>
  <Routes>

<Route path="/" element={<Home/>}/>
<Route path="/Services" element={<Services/>}/>
<Route path="/Login" element={<Login/>}/>
<Route path="/Signup" element={<Signup/>}/>
<Route path="/Book" element={<Book/>}/>

  </Routes>
</Router>
</>    
  )
}


export default App;