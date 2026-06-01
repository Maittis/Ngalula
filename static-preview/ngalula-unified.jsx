import { useState, useMemo, useEffect, useCallback } from "react";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid, Cell } from "recharts";

// ─── SHARED SEED DATA ─────────────────────────────────────────────────────
const SERVICES_LIST = [
  {name:"Swedish Massage",                              cat:"Massage",                price:550},
  {name:"Aromatherapy Massage",                         cat:"Massage",                price:650},
  {name:"Pregnancy Massage",                            cat:"Massage",                price:800},
  {name:"Ngalula Signature Massage",                    cat:"Massage",                price:800},
  {name:"Balcony Massage",                              cat:"Massage",                price:800},
  {name:"Ngalula Yondolola Massage",                    cat:"Massage",                price:850},
  {name:"Deep Tissue Massage",                          cat:"Massage",                price:850},
  {name:"Ukuchina Massage",                             cat:"Massage",                price:950},
  {name:"Ngalula Recovery Massage",                     cat:"Massage",                price:1250},
  {name:"Ngalula Healing Massage",                      cat:"Massage",                price:1250},
  {name:"Ngalula Post-Surgery/Post-Sickness Massage",   cat:"Massage",                price:1250},
  {name:"Ngalula New Mommy Massage",                    cat:"Massage",                price:1250},
  {name:"Couples' Massage",                             cat:"Massage",                price:1500},
  {name:"Ngalula Full Gel Pedicure",                    cat:"Pedicures & Body Scrubs",price:450},
  {name:"Men's Premium Pedicure",                       cat:"Pedicures & Body Scrubs",price:525},
  {name:"Men's Premium Pedicure & Manicure",            cat:"Pedicures & Body Scrubs",price:800},
  {name:"Body Scrub",                                   cat:"Pedicures & Body Scrubs",price:550},
  {name:"Body Scrub and Wipe Off",                      cat:"Pedicures & Body Scrubs",price:750},
  {name:"Steaming Body Scrub",                          cat:"Pedicures & Body Scrubs",price:850},
  {name:"Full Body Scrub Experience",                   cat:"Pedicures & Body Scrubs",price:1000},
  {name:"Full Body Scrub Experience & Swedish Massage", cat:"Pedicures & Body Scrubs",price:1500},
  {name:"Deep Cleanse Facial",                          cat:"Facials",                price:612},
  {name:"Extraction Facial",                            cat:"Facials",                price:850},
  {name:"Dermaplaning Facial",                          cat:"Facials",                price:850},
  {name:"Luxurious Facial",                             cat:"Facials",                price:1100},
  {name:"Standard Manicure",                            cat:"Manicure",               price:250},
  {name:"Gel Paint Manicure",                           cat:"Manicure",               price:285},
  {name:"Rubber Gel Paint Manicure",                    cat:"Manicure",               price:305},
  {name:"Manicure with Stickons",                       cat:"Manicure",               price:385},
  {name:"Manicure with Extensions / Polygel",           cat:"Manicure",               price:450},
];

const SEED_THERAPISTS = [
  {id:1,name:"Maria",    role:"Senior Massage Therapist",initials:"AN",color:"#c9a96e",specialties:"Massage, Body Treatments",    phone:"+260 97 111 2233",email:"aisha@ngalulaspa.com",   bio:"5+ years in therapeutic massage.",  sessions:142,rating:4.9,revenue:68400,active:true},
  {id:2,name:"Abigail",role:"Beauty Therapist",        initials:"PC",color:"#d4a8ff",specialties:"Manicure, Pedicures",          phone:"+260 96 222 3344",email:"priscilla@ngalulaspa.com",bio:"Nail artistry & gel manicure expert.",sessions:98, rating:4.8,revenue:42100,active:true},
  {id:3,name:"Grace",   role:"Skin & Facial Specialist",initials:"GM",color:"#5cdb95",specialties:"Facials, Aromatherapy",        phone:"+260 95 333 4455",email:"grace@ngalulaspa.com",    bio:"Certified dermaplaning specialist.", sessions:76, rating:4.9,revenue:38600,active:true},
  {id:3,name:"Memory",   role:"Skin & Facial Specialist",initials:"GM",color:"#5cdb95",specialties:"Facials, Aromatherapy",        phone:"+260 95 333 4455",email:"grace@ngalulaspa.com",    bio:"Certified dermaplaning specialist.", sessions:76, rating:4.9,revenue:38600,active:true},
];

const SEED_BOOKINGS = [
  {id:101,ref:"NGS-SEED01",client:"Thandiwe Mwanza",  phone:"+260 97 123 4567",email:"t@mail.com", service:"Ngalula Signature Massage",   cat:"Massage",                therapist:"Aisha Nkonde",    date:"2026-05-17",time:"09:00",amount:800, status:"confirmed",  payment:"paid",  note:"Regular client.",source:"existing"},
  {id:102,ref:"NGS-SEED02",client:"Chisomo Banda",    phone:"+260 96 234 5678",email:"",           service:"Swedish Massage",             cat:"Massage",                therapist:"Priscilla Chanda",date:"2026-05-17",time:"10:30",amount:550, status:"confirmed",  payment:"unpaid",note:"",source:"existing"},
  {id:103,ref:"NGS-SEED03",client:"Mutale Kabwe",     phone:"+260 95 345 6789",email:"m@mail.com", service:"Deep Cleanse Facial",         cat:"Facials",                therapist:"Grace Mulenga",   date:"2026-05-17",time:"11:00",amount:612, status:"in_progress",payment:"paid",  note:"",source:"existing"},
  {id:104,ref:"NGS-SEED04",client:"Natasha Phiri",    phone:"+260 97 456 7890",email:"n@mail.com", service:"Couples' Massage",            cat:"Massage",                therapist:"Aisha Nkonde",    date:"2026-05-17",time:"13:00",amount:1500,status:"pending",    payment:"unpaid",note:"Anniversary — rose petals.",source:"existing"},
  {id:105,ref:"NGS-SEED05",client:"Yvonne Musonda",   phone:"+260 96 890 1234",email:"y@mail.com", service:"Luxurious Facial",            cat:"Facials",                therapist:"Grace Mulenga",   date:"2026-05-16",time:"09:00",amount:1100,status:"completed",  payment:"paid",  note:"",source:"existing"},
  {id:106,ref:"NGS-SEED06",client:"Susan Lungu",      phone:"+260 97 012 3456",email:"s@mail.com", service:"Ngalula New Mommy Massage",   cat:"Massage",                therapist:"Aisha Nkonde",    date:"2026-05-16",time:"14:00",amount:1250,status:"completed",  payment:"paid",  note:"6 weeks postpartum.",source:"existing"},
  {id:107,ref:"NGS-SEED07",client:"Emmanuel Zulu",    phone:"+260 95 678 9012",email:"e@mail.com", service:"Men's Premium Pedicure",      cat:"Pedicures & Body Scrubs",therapist:"Grace Mulenga",   date:"2026-05-18",time:"09:30",amount:525, status:"confirmed",  payment:"paid",  note:"",source:"existing"},
  {id:108,ref:"NGS-SEED08",client:"Lweendo Mwape",    phone:"+260 95 222 3333",email:"",           service:"Dermaplaning Facial",         cat:"Facials",                therapist:"Grace Mulenga",   date:"2026-05-18",time:"11:00",amount:850, status:"pending",    payment:"unpaid",note:"",source:"existing"},
];

const REVENUE_DATA = [
  {day:"May 4",rev:1850},{day:"May 5",rev:2400},{day:"May 6",rev:1200},{day:"May 7",rev:3100},
  {day:"May 8",rev:2750},{day:"May 9",rev:950}, {day:"May 10",rev:1650},{day:"May 11",rev:2800},
  {day:"May 12",rev:3400},{day:"May 13",rev:1900},{day:"May 14",rev:2200},{day:"May 15",rev:3800},
  {day:"May 16",rev:3500},{day:"May 17",rev:2787},
];

const TIME_SLOTS = ["08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00","17:30"];
const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const DAYS   = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
const CAT_META = {"All":{icon:"🌸",col:"#c9a96e",bg:"rgba(201,169,110,0.1)"},"Massage":{icon:"🫧",col:"#c9a96e",bg:"rgba(201,169,110,0.1)"},"Pedicures & Body Scrubs":{icon:"🌿",col:"#5cdb95",bg:"rgba(92,219,149,0.1)"},"Facials":{icon:"✨",col:"#d4a8ff",bg:"rgba(212,168,255,0.1)"},"Manicure":{icon:"💅",col:"#ff9eb5",bg:"rgba(255,158,181,0.1)"}};
const STATUS_META = {pending:{l:"Pending",c:"#ffb347",bg:"rgba(255,179,71,0.12)"},confirmed:{l:"Confirmed",c:"#5cdb95",bg:"rgba(92,219,149,0.12)"},in_progress:{l:"In Progress",c:"#8b9ef7",bg:"rgba(139,158,247,0.12)"},completed:{l:"Completed",c:"#c9a96e",bg:"rgba(201,169,110,0.12)"},cancelled:{l:"Cancelled",c:"#ff4d6d",bg:"rgba(255,77,109,0.12)"}};
const PAY_META   = {unpaid:{l:"Unpaid",c:"#ff4d6d"},paid:{l:"Paid ✓",c:"#5cdb95"},partial:{l:"Partial",c:"#ffb347"}};
const COLORS_PRESET = ["#c9a96e","#d4a8ff","#5cdb95","#ff9eb5","#8b9ef7","#ffb347","#5bc8f5","#f97d6e"];
const AIRTEL_NUMBER = "0971 234 567";

const genRef = () => "NGS-" + Math.random().toString(36).slice(2,8).toUpperCase();
const genId  = () => Date.now() + Math.floor(Math.random()*9999);
const fmtDate = d => `${DAYS[d.getDay()]} ${d.getDate()} ${MONTHS[d.getMonth()]}`;
const getDates = () => { const out=[]; const d=new Date(); d.setDate(d.getDate()+1); while(out.length<21){if(d.getDay()!==0)out.push(new Date(d));d.setDate(d.getDate()+1);} return out; };

// ─── SHARED TINY COMPONENTS ───────────────────────────────────────────────
const SBadge = ({s}) => { const m=STATUS_META[s]||STATUS_META.pending; return <span style={{padding:"0.17rem 0.55rem",borderRadius:"20px",fontSize:"0.64rem",fontWeight:600,background:m.bg,color:m.c,whiteSpace:"nowrap"}}>{m.l}</span>; };
const PBadge = ({s}) => { const m=PAY_META[s]||PAY_META.unpaid; return <span style={{fontSize:"0.7rem",color:m.c,fontWeight:600}}>{m.l}</span>; };

const SI = {width:"100%",background:"#0d0c13",border:"1px solid #2a2633",borderRadius:"8px",padding:"0.58rem 0.85rem",color:"#e8d5b7",fontSize:"0.83rem",outline:"none",boxSizing:"border-box",fontFamily:"'DM Sans',sans-serif"};
const PrimaryBtn = ({children,onClick,disabled,style={}}) => <button onClick={onClick} disabled={disabled} style={{padding:"0.7rem 1.5rem",borderRadius:"9px",border:"none",cursor:disabled?"not-allowed":"pointer",fontWeight:700,fontSize:"0.83rem",fontFamily:"'DM Sans',sans-serif",background:"linear-gradient(135deg,#c9a96e,#e8c98a)",color:"#0d0b10",opacity:disabled?0.35:1,transition:"opacity 0.2s",...style}}>{children}</button>;
const GhostBtn  = ({children,onClick,style={}}) => <button onClick={onClick} style={{padding:"0.65rem 1.2rem",borderRadius:"9px",cursor:"pointer",fontWeight:500,fontSize:"0.82rem",fontFamily:"'DM Sans',sans-serif",background:"transparent",color:"#6e6460",border:"1px solid #2a2633",...style}}>{children}</button>;
const DangerBtn = ({children,onClick}) => <button onClick={onClick} style={{padding:"0.65rem 1.2rem",borderRadius:"9px",cursor:"pointer",fontWeight:700,fontSize:"0.82rem",fontFamily:"'DM Sans',sans-serif",background:"rgba(255,77,109,0.1)",color:"#ff4d6d",border:"1px solid rgba(255,77,109,0.3)"}}>{ children}</button>;

function FField({label,error,children}){return(<div style={{marginBottom:"0.82rem"}}><label style={{display:"block",fontSize:"0.63rem",color:"#8a7f70",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.3rem"}}>{label}</label>{children}{error&&<div style={{fontSize:"0.63rem",color:"#ff4d6d",marginTop:"0.2rem"}}>⚠ {error}</div>}</div>);}

function BaseModal({title,subtitle,onClose,children,wide=false}){return(<div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.8)",backdropFilter:"blur(6px)",display:"flex",alignItems:"center",justifyContent:"center",zIndex:300,padding:"1rem"}}><div style={{background:"#13111a",border:"1px solid #2a2633",borderRadius:"16px",padding:"1.8rem",width:"100%",maxWidth:wide?"700px":"460px",maxHeight:"92vh",overflowY:"auto",boxShadow:"0 30px 80px rgba(0,0,0,0.7)"}}><div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"1.4rem"}}><div><h3 style={{margin:0,fontFamily:"'Cormorant Garamond',serif",fontSize:"1.2rem",color:"#e8d5b7"}}>{title}</h3>{subtitle&&<p style={{margin:"0.2rem 0 0",fontSize:"0.7rem",color:"#5a5060"}}>{subtitle}</p>}</div><button onClick={onClose} style={{background:"none",border:"none",color:"#5a5060",fontSize:"1.5rem",cursor:"pointer",lineHeight:1,flexShrink:0}}>×</button></div>{children}</div></div>);}

function DeleteConfirm({what,name,onConfirm,onClose}){return(<BaseModal title="Confirm Deletion" onClose={onClose}><div style={{textAlign:"center",padding:"0.5rem 0 1.5rem"}}><div style={{fontSize:"2.5rem",marginBottom:"0.8rem"}}>🗑️</div><p style={{color:"#a89f8c",fontSize:"0.85rem",lineHeight:1.6,margin:"0 0 0.4rem"}}>Permanently delete this {what}:</p><p style={{color:"#e8d5b7",fontWeight:600,fontSize:"0.9rem",margin:"0 0 1rem"}}>{name}</p><p style={{color:"#ff4d6d",fontSize:"0.72rem",margin:0}}>This cannot be undone.</p></div><div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><DangerBtn onClick={onConfirm}>Delete</DangerBtn></div></BaseModal>);}

// ─── MODE SWITCHER BAR ────────────────────────────────────────────────────
function ModeSwitcher({mode,setMode,newCount,pulseAdmin}){
  return(
    <div style={{position:"fixed",top:"12px",left:"50%",transform:"translateX(-50%)",zIndex:500,display:"flex",background:"rgba(13,11,20,0.95)",backdropFilter:"blur(16px)",border:"1px solid #2a2633",borderRadius:"40px",padding:"4px",gap:"3px",boxShadow:"0 8px 32px rgba(0,0,0,0.5)"}}>
      {[["client","👤 Book Appointment","client"],["admin","⚙️ Admin Panel","admin"]].map(([m,label,key])=>{
        const active=mode===m;
        return(
          <button key={key} onClick={()=>setMode(m)} style={{position:"relative",padding:"0.42rem 1.1rem",borderRadius:"36px",border:"none",background:active?"linear-gradient(135deg,#c9a96e,#e8c98a)":"transparent",color:active?"#0d0b10":"#5a5060",cursor:"pointer",fontWeight:active?700:400,fontSize:"0.78rem",fontFamily:"'DM Sans',sans-serif",transition:"all 0.2s",whiteSpace:"nowrap"}}>
            {label}
            {key==="admin" && newCount>0 && (
              <span style={{position:"absolute",top:"-4px",right:"-4px",background:"#ff4d6d",color:"#fff",borderRadius:"50%",width:"16px",height:"16px",fontSize:"0.55rem",fontWeight:700,display:"flex",alignItems:"center",justifyContent:"center",animation:pulseAdmin?"pulse 1s ease infinite":undefined}}>{newCount}</span>
            )}
          </button>
        );
      })}
      <style>{`@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.25)}} @keyframes spin{to{transform:rotate(360deg)}} @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}} @keyframes slideIn{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)}}`}</style>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
//  CLIENT BOOKING FLOW
// ═══════════════════════════════════════════════════════════════════════════

function ProgressBar({step}){
  const steps=["Services","Date & Time","Therapist","Review","Account"];
  return(
    <div style={{background:"#0f0d14",borderBottom:"1px solid #1e1c26",padding:"0.85rem 1.5rem",overflowX:"auto"}}>
      <div style={{display:"flex",alignItems:"center",justifyContent:"center",minWidth:"340px"}}>
        {steps.map((s,i)=>{
          const n=i+1,done=step>n,active=step===n;
          return(
            <div key={s} style={{display:"flex",alignItems:"center"}}>
              <div style={{display:"flex",flexDirection:"column",alignItems:"center",gap:"0.15rem"}}>
                <div style={{width:"24px",height:"24px",borderRadius:"50%",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.68rem",fontWeight:700,background:done?"#c9a96e":active?"rgba(201,169,110,0.15)":"#1a1823",color:done?"#0d0b10":active?"#c9a96e":"#3a3650",border:active?"2px solid #c9a96e":"2px solid transparent",transition:"all 0.3s"}}>{done?"✓":n}</div>
                <span style={{fontSize:"0.58rem",color:active?"#c9a96e":done?"#6e6460":"#3a3650",whiteSpace:"nowrap",letterSpacing:"0.05em"}}>{s}</span>
              </div>
              {i<steps.length-1&&<div style={{width:"clamp(20px,4vw,44px)",height:"1px",background:done?"#c9a96e66":"#2a2633",margin:"0 3px 1rem 3px",transition:"background 0.3s"}}/>}
            </div>
          );
        })}
      </div>
    </div>
  );
}

function HeroSection({onBook,heroImageUrl}){
  return(
    <div style={{minHeight:"100vh",display:"flex",flexDirection:"column",alignItems:"center",justifyContent:"center",position:"relative",overflow:"hidden",textAlign:"center",padding:"6rem 1.5rem 2rem"}}>
      <div style={{position:"absolute",inset:0,backgroundColor:"#08070f",backgroundImage:heroImageUrl?`url(${heroImageUrl})`:"none",backgroundSize:"cover",backgroundPosition:"center",backgroundRepeat:"no-repeat",filter:"brightness(0.45) saturate(1.05)"}}/>
      <div style={{position:"absolute",inset:0,background:"linear-gradient(180deg,rgba(8,7,15,0.72) 0%,rgba(8,7,15,0.92) 100%)"}}/>
      <div style={{position:"absolute",inset:0,background:"radial-gradient(ellipse 90% 60% at 50% 35%,rgba(201,169,110,0.07),transparent 65%)"}}/>
      <div style={{position:"absolute",inset:0,background:"radial-gradient(ellipse 50% 70% at 15% 85%,rgba(92,219,149,0.04),transparent 55%)"}}/>
      {[700,500,320,160].map((s,i)=><div key={s} style={{position:"absolute",width:`${s}px`,height:`${s}px`,borderRadius:"50%",border:`1px solid rgba(201,169,110,${0.03+i*0.02})`,top:"50%",left:"50%",transform:"translate(-50%,-50%)",pointerEvents:"none"}}/>)}
      <div style={{position:"relative",zIndex:2,maxWidth:"520px",animation:"fadeIn 0.7s ease both"}}>
        <div style={{marginBottom:"1.5rem",fontSize:"2.8rem",filter:"drop-shadow(0 0 20px rgba(201,169,110,0.3))"}}>🌸</div>
        <p style={{fontFamily:"'Cormorant Garamond',serif",fontStyle:"italic",fontSize:"0.82rem",color:"#6e6460",letterSpacing:"0.25em",textTransform:"uppercase",margin:"0 0 0.4rem"}}>Welcome to</p>
        <h1 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"clamp(2.8rem,7vw,5rem)",fontWeight:600,color:"#e8d5b7",margin:"0 0 0.4rem",lineHeight:1.05,letterSpacing:"0.03em"}}>Ngalula Spa</h1>
        <div style={{width:"60px",height:"1px",background:"linear-gradient(90deg,transparent,#c9a96e,transparent)",margin:"1rem auto"}}/>
        <p style={{fontSize:"0.95rem",color:"#7a7068",lineHeight:1.8,margin:"0 auto 2.5rem",fontWeight:300}}>Indulge in transformative wellness experiences crafted for your body, mind &amp; spirit.</p>
        <PrimaryBtn onClick={onBook} style={{padding:"1rem 3rem",fontSize:"1rem",borderRadius:"12px",boxShadow:"0 10px 40px rgba(201,169,110,0.2)"}}>Book Your Session ✦</PrimaryBtn>
        <div style={{display:"flex",gap:"0.5rem",justifyContent:"center",marginTop:"2.5rem",flexWrap:"wrap"}}>
          {["🫧 Massage","✨ Facials","💅 Nails","🌿 Body Treatments"].map(f=><span key={f} style={{padding:"0.3rem 0.85rem",borderRadius:"20px",border:"1px solid rgba(201,169,110,0.15)",color:"#4a4560",fontSize:"0.7rem"}}>{f}</span>)}
        </div>
      </div>
    </div>
  );
}

function ServiceSelector({cart,toggle,total,onNext}){
  const [catF,setCatF]=useState("All");
  const cats=Object.keys(CAT_META);
  const filtered=catF==="All"?SERVICES_LIST:SERVICES_LIST.filter(s=>s.cat===catF);
  return(
    <div style={{maxWidth:"740px",margin:"0 auto",padding:"1.8rem 1.5rem 7rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Choose Your Services</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Select one or more — your total updates live.</p>
      <div style={{display:"flex",gap:"0.4rem",marginBottom:"1.2rem",flexWrap:"wrap"}}>
        {cats.map(c=>{const m=CAT_META[c],act=catF===c;return(<button key={c} onClick={()=>setCatF(c)} style={{padding:"0.28rem 0.85rem",borderRadius:"20px",border:`1px solid ${act?m.col:"#2a2633"}`,background:act?m.bg:"transparent",color:act?m.col:"#4a4560",cursor:"pointer",fontSize:"0.74rem",fontFamily:"'DM Sans',sans-serif"}}>{m.icon} {c}</button>);})}
      </div>
      <div style={{display:"flex",flexDirection:"column",gap:"0.4rem"}}>
        {filtered.map(svc=>{
          const inCart=cart.some(i=>i.name===svc.name),m=CAT_META[svc.cat];
          return(
            <div key={svc.name} onClick={()=>toggle(svc)} style={{display:"flex",justifyContent:"space-between",alignItems:"center",padding:"0.85rem 1rem",background:inCart?m.bg:"#13111a",border:`1px solid ${inCart?m.col+"55":"#1e1c26"}`,borderRadius:"11px",cursor:"pointer",transition:"all 0.15s",gap:"0.8rem"}}>
              <div style={{flex:1,minWidth:0}}>
                <div style={{fontWeight:500,color:inCart?"#e8d5b7":"#a89f8c",fontSize:"0.86rem",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{svc.name}</div>
                <div style={{fontSize:"0.67rem",color:"#4a4560",marginTop:"0.15rem"}}>{m.icon} {svc.cat}</div>
              </div>
              <div style={{display:"flex",alignItems:"center",gap:"0.7rem",flexShrink:0}}>
                <span style={{fontWeight:700,color:m.col,fontSize:"0.86rem"}}>K{svc.price.toLocaleString()}</span>
                <div style={{width:"22px",height:"22px",borderRadius:"6px",border:`2px solid ${inCart?m.col:"#2a2633"}`,background:inCart?m.col:"transparent",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.78rem",color:inCart?"#0d0b10":"#3a3650",fontWeight:700}}>{inCart?"✓":"+"}</div>
              </div>
            </div>
          );
        })}
      </div>
      <div style={{position:"fixed",bottom:0,left:0,right:0,background:"rgba(13,11,16,0.97)",backdropFilter:"blur(12px)",borderTop:"1px solid #2a2633",padding:"1rem 1.5rem",display:"flex",alignItems:"center",justifyContent:"space-between",zIndex:50,gap:"1rem"}}>
        <div>
          <div style={{fontSize:"0.63rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em"}}>{cart.length} service{cart.length!==1?"s":""} selected</div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.5rem",color:"#c9a96e",fontWeight:600,lineHeight:1.1}}>K{total.toLocaleString()}</div>
        </div>
        <PrimaryBtn onClick={onNext} disabled={cart.length===0}>Continue →</PrimaryBtn>
      </div>
    </div>
  );
}

function DateTimePicker({dates,selDate,setSelDate,selTime,setSelTime,bookings,therapistName,onBack,onNext}){
  const takenTimes = useMemo(()=>{
    if(!selDate||!therapistName) return [];
    const d=selDate.toISOString().slice(0,10);
    return bookings.filter(b=>b.therapist===therapistName&&b.date===d&&b.status!=="cancelled").map(b=>b.time);
  },[selDate,therapistName,bookings]);

  return(
    <div style={{maxWidth:"680px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Choose Date &amp; Time</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Availability is live — taken slots are shown in real time.</p>
      <div style={{display:"flex",gap:"0.5rem",overflowX:"auto",paddingBottom:"0.7rem",marginBottom:"1.4rem",scrollbarWidth:"none"}}>
        {dates.map((d,i)=>{
          const act=selDate&&d.toDateString()===selDate.toDateString();
          return(<div key={i} onClick={()=>{setSelDate(d);setSelTime(null);}} style={{flexShrink:0,width:"56px",padding:"0.65rem 0.3rem",borderRadius:"12px",textAlign:"center",cursor:"pointer",background:act?"linear-gradient(135deg,#c9a96e,#e8c98a)":"#13111a",border:`1px solid ${act?"#c9a96e":"#1e1c26"}`,color:act?"#0d0b10":"#5a5060",transition:"all 0.2s"}}>
            <div style={{fontSize:"0.58rem",fontWeight:700,letterSpacing:"0.08em",textTransform:"uppercase"}}>{DAYS[d.getDay()]}</div>
            <div style={{fontSize:"1.2rem",fontWeight:700,margin:"0.12rem 0",fontFamily:"'Cormorant Garamond',serif"}}>{d.getDate()}</div>
            <div style={{fontSize:"0.56rem"}}>{MONTHS[d.getMonth()]}</div>
          </div>);
        })}
      </div>
      {selDate?(
        <>
          <p style={{color:"#5a5060",fontSize:"0.76rem",marginBottom:"0.7rem"}}>Times for <span style={{color:"#c9a96e"}}>{fmtDate(selDate)}</span> — <span style={{color:"#ff4d6d"}}>red = taken by another booking</span></p>
          <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fill,minmax(72px,1fr))",gap:"0.45rem",marginBottom:"2rem"}}>
            {TIME_SLOTS.map(t=>{
              const taken=takenTimes.includes(t),act=selTime===t;
              return(<button key={t} onClick={()=>!taken&&setSelTime(t)} style={{padding:"0.55rem",borderRadius:"9px",border:`1px solid ${taken?"#ff4d6d33":act?"#c9a96e":"#1e1c26"}`,background:taken?"rgba(255,77,109,0.06)":act?"rgba(201,169,110,0.15)":"#13111a",color:taken?"#ff4d6d44":act?"#c9a96e":"#5a5060",cursor:taken?"not-allowed":"pointer",fontSize:"0.8rem",fontWeight:act?600:400,fontFamily:"'DM Sans',sans-serif",textDecoration:taken?"line-through":"none"}}>{t}</button>);
            })}
          </div>
        </>
      ):(
        <div style={{padding:"2.5rem",textAlign:"center",color:"#2a2633",fontSize:"0.8rem",border:"1px dashed #1e1c26",borderRadius:"12px",marginBottom:"1.5rem"}}>← Pick a date to see live availability</div>
      )}
      <div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onBack}>← Back</GhostBtn><PrimaryBtn onClick={onNext} disabled={!selDate||!selTime} style={{flex:1}}>Continue →</PrimaryBtn></div>
    </div>
  );
}

function TherapistPicker({therapists,bookings,selDate,selTime,selected,setSelected,onBack,onNext}){
  const isAvail=(t)=>{
    if(!selDate||!selTime) return true;
    const d=selDate.toISOString().slice(0,10);
    return !bookings.some(b=>b.therapist===t.name&&b.date===d&&b.time===selTime&&b.status!=="cancelled");
  };
  const active=therapists.filter(t=>t.active);
  return(
    <div style={{maxWidth:"680px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Choose Your Therapist</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Live availability for your selected date &amp; time. Unavailable = already booked.</p>
      <div style={{display:"flex",flexDirection:"column",gap:"0.85rem",marginBottom:"1.8rem"}}>
        {active.map(t=>{
          const avail=isAvail(t),isSel=selected?.id===t.id;
          return(
            <div key={t.id} onClick={()=>avail&&setSelected(t)} style={{display:"flex",gap:"1rem",alignItems:"center",padding:"1.1rem",background:isSel?`${t.color}10`:"#13111a",border:`1px solid ${isSel?t.color+"55":"#1e1c26"}`,borderRadius:"13px",cursor:avail?"pointer":"not-allowed",opacity:avail?1:0.45,transition:"all 0.2s"}}>
              <div style={{width:"48px",height:"48px",borderRadius:"50%",background:`${t.color}15`,border:`2px solid ${t.color}44`,display:"flex",alignItems:"center",justifyContent:"center",fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:t.color,fontWeight:700,flexShrink:0}}>{t.initials}</div>
              <div style={{flex:1,minWidth:0}}>
                <div style={{display:"flex",alignItems:"center",gap:"0.6rem",flexWrap:"wrap",marginBottom:"0.12rem"}}>
                  <span style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.9rem"}}>{t.name}</span>
                  <span style={{padding:"0.14rem 0.52rem",borderRadius:"20px",fontSize:"0.62rem",fontWeight:600,background:avail?"rgba(92,219,149,0.1)":"rgba(255,77,109,0.1)",color:avail?"#5cdb95":"#ff4d6d"}}>{avail?"✓ Available":"✗ Booked"}</span>
                </div>
                <div style={{fontSize:"0.7rem",color:t.color,marginBottom:"0.18rem"}}>{t.role}</div>
                <div style={{fontSize:"0.7rem",color:"#4a4560"}}>{t.bio}</div>
                <div style={{display:"flex",gap:"0.35rem",marginTop:"0.3rem",flexWrap:"wrap"}}>
                  {t.specialties.split(",").map(s=><span key={s} style={{padding:"0.1rem 0.45rem",borderRadius:"20px",background:"rgba(255,255,255,0.04)",color:"#4a4560",fontSize:"0.6rem"}}>{s.trim()}</span>)}
                </div>
              </div>
              {isSel&&<span style={{color:t.color,fontSize:"1.2rem",flexShrink:0}}>✦</span>}
            </div>
          );
        })}
      </div>
      <div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onBack}>← Back</GhostBtn><PrimaryBtn onClick={onNext} disabled={!selected} style={{flex:1}}>Continue →</PrimaryBtn></div>
    </div>
  );
}

function ReviewStep({cart,total,selDate,selTime,therapist,onBack,onNext}){
  return(
    <div style={{maxWidth:"560px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.7rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>Review Your Booking</h2>
      <p style={{color:"#5a5060",fontSize:"0.8rem",marginBottom:"1.4rem"}}>Confirm everything looks right before paying.</p>
      <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"13px",padding:"1.2rem",marginBottom:"0.9rem"}}>
        <div style={{fontSize:"0.63rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.9rem"}}>Selected Services</div>
        {cart.map(s=><div key={s.name} style={{display:"flex",justifyContent:"space-between",fontSize:"0.83rem",padding:"0.3rem 0",borderBottom:"1px solid #1a1823"}}><span style={{color:"#a89f8c",flex:1,minWidth:0,overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap",paddingRight:"0.5rem"}}>{s.name}</span><span style={{color:"#c9a96e",fontWeight:600,flexShrink:0}}>K{s.price.toLocaleString()}</span></div>)}
        <div style={{display:"flex",justifyContent:"space-between",marginTop:"0.7rem",paddingTop:"0.6rem",borderTop:"1px solid #2a2633"}}>
          <span style={{color:"#5a5060",fontSize:"0.8rem"}}>Total</span>
          <span style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.4rem",color:"#c9a96e",fontWeight:600}}>K{total.toLocaleString()}</span>
        </div>
      </div>
      <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"13px",padding:"1.2rem",marginBottom:"0.9rem"}}>
        <div style={{fontSize:"0.63rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.8rem"}}>Appointment</div>
        {[{i:"📅",v:selDate?fmtDate(selDate):"—"},{i:"🕐",v:selTime||"—"},{i:"👤",v:therapist?.name||"—"},{i:"💼",v:therapist?.role||"—"}].map((r,idx)=>(
          <div key={idx} style={{display:"flex",gap:"0.6rem",fontSize:"0.82rem",padding:"0.3rem 0",borderBottom:"1px solid #1a1823"}}><span style={{flexShrink:0}}>{r.i}</span><span style={{color:"#8a7f70"}}>{r.v}</span></div>
        ))}
      </div>
      <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"13px",padding:"1rem",marginBottom:"1.4rem",display:"flex",alignItems:"center",gap:"0.9rem"}}>
        <div style={{width:"36px",height:"36px",borderRadius:"9px",background:"#c0392b",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"1.1rem",flexShrink:0}}>📱</div>
        <div><div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.88rem"}}>Airtel Money</div><div style={{fontSize:"0.7rem",color:"#5a5060"}}>Mobile money transfer</div></div>
        <span style={{marginLeft:"auto",color:"#5cdb95",fontSize:"0.7rem",fontWeight:700}}>✓ Selected</span>
      </div>
      <div style={{display:"flex",gap:"0.8rem"}}><GhostBtn onClick={onBack}>← Back</GhostBtn><PrimaryBtn onClick={onNext} style={{flex:1}}>Confirm Payment →</PrimaryBtn></div>
    </div>
  );
}

function AuthStep({total,onSubmit,onBack}){
  const [mode,setMode]=useState("signup");
  const [form,setForm]=useState({name:"",email:"",phone:"",password:""});
  const [errors,setErrors]=useState({});
  const [loading,setLoading]=useState(false);
  const upd=(k,v)=>setForm(f=>({...f,[k]:v}));

  const validate=()=>{
    const e={};
    if(mode==="signup"&&!form.name.trim()) e.name="Required";
    if(!form.email||!form.email.includes("@")) e.email="Valid email required";
    if(!form.phone||form.phone.length<9) e.phone="Valid phone required";
    if(!form.password||form.password.length<6) e.password="Min 6 characters";
    setErrors(e); return !Object.keys(e).length;
  };

  const handle=()=>{ if(!validate()) return; setLoading(true); setTimeout(()=>{setLoading(false);onSubmit(form);},1800); };

  return(
    <div style={{maxWidth:"380px",margin:"0 auto",padding:"1.8rem 1.5rem"}}>
      <div style={{background:"#13111a",border:"1px solid #2a2633",borderRadius:"16px",padding:"1.8rem"}}>
        <div style={{textAlign:"center",marginBottom:"1.4rem"}}>
          <div style={{fontSize:"2rem",marginBottom:"0.5rem"}}>🔐</div>
          <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.45rem",color:"#e8d5b7",margin:"0 0 0.25rem"}}>{mode==="signup"?"Create Account":"Welcome Back"}</h2>
          <p style={{color:"#4a4560",fontSize:"0.76rem",margin:0}}>{mode==="signup"?"Sign up to secure your booking":"Log in to complete your booking"}</p>
        </div>
        <div style={{display:"flex",background:"#0f0e13",borderRadius:"9px",padding:"3px",marginBottom:"1.2rem"}}>
          {["signup","login"].map(m=><button key={m} onClick={()=>{setMode(m);setErrors({});}} style={{flex:1,padding:"0.48rem",borderRadius:"7px",border:"none",background:mode===m?"#c9a96e":"transparent",color:mode===m?"#0d0b10":"#4a4560",cursor:"pointer",fontSize:"0.78rem",fontWeight:mode===m?700:400,fontFamily:"'DM Sans',sans-serif"}}>{m==="signup"?"Sign Up":"Log In"}</button>)}
        </div>
        <div style={{display:"flex",flexDirection:"column",gap:"0.2rem",marginBottom:"1rem"}}>
          {mode==="signup"&&<FField label="Full Name" error={errors.name}><input style={{...SI,borderColor:errors.name?"#ff4d6d55":"#2a2633"}} value={form.name} onChange={e=>upd("name",e.target.value)} placeholder="Thandiwe Mwanza"/></FField>}
          <FField label="Email" error={errors.email}><input style={{...SI,borderColor:errors.email?"#ff4d6d55":"#2a2633"}} type="email" value={form.email} onChange={e=>upd("email",e.target.value)} placeholder="you@example.com"/></FField>
          <FField label="Phone (Airtel)" error={errors.phone}><input style={{...SI,borderColor:errors.phone?"#ff4d6d55":"#2a2633"}} type="tel" value={form.phone} onChange={e=>upd("phone",e.target.value)} placeholder="097X XXX XXX"/></FField>
          <FField label="Password" error={errors.password}><input style={{...SI,borderColor:errors.password?"#ff4d6d55":"#2a2633"}} type="password" value={form.password} onChange={e=>upd("password",e.target.value)} placeholder="Min 6 characters"/></FField>
        </div>
        <div style={{padding:"0.7rem 1rem",background:"rgba(201,169,110,0.08)",border:"1px solid rgba(201,169,110,0.18)",borderRadius:"9px",display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"1.1rem"}}>
          <span style={{color:"#5a5060",fontSize:"0.78rem"}}>Amount via Airtel Money</span>
          <span style={{fontFamily:"'Cormorant Garamond',serif",color:"#c9a96e",fontWeight:700,fontSize:"1.1rem"}}>K{total.toLocaleString()}</span>
        </div>
        <button onClick={handle} disabled={loading} style={{...{width:"100%",padding:"0.85rem",borderRadius:"9px",border:"none",background:"linear-gradient(135deg,#c9a96e,#e8c98a)",color:"#0d0b10",fontWeight:700,fontSize:"0.88rem",fontFamily:"'DM Sans',sans-serif",cursor:loading?"not-allowed":"pointer",opacity:loading?0.7:1}}}>
          {loading?<span style={{display:"flex",alignItems:"center",justifyContent:"center",gap:"0.5rem"}}><span style={{width:"13px",height:"13px",border:"2px solid rgba(13,11,16,0.3)",borderTopColor:"#0d0b10",borderRadius:"50%",animation:"spin 0.8s linear infinite",display:"inline-block"}}/>Processing…</span>:"Confirm & Pay with Airtel Money 📱"}
        </button>
        <GhostBtn onClick={onBack} style={{width:"100%",marginTop:"0.6rem"}}>← Back</GhostBtn>
      </div>
    </div>
  );
}

function ConfirmationStep({bookingRef,total,therapist,selDate,selTime,cart,onSwitchAdmin}){
  const [copied,setCopied]=useState(false);
  const copy=()=>{ try{navigator.clipboard.writeText(bookingRef);}catch(_){} setCopied(true); setTimeout(()=>setCopied(false),2000); };
  return(
    <div style={{maxWidth:"500px",margin:"0 auto",padding:"2rem 1.5rem",animation:"fadeIn 0.5s ease both"}}>
      <div style={{textAlign:"center",marginBottom:"1.8rem"}}>
        <div style={{width:"66px",height:"66px",borderRadius:"50%",background:"rgba(92,219,149,0.12)",border:"2px solid #5cdb9566",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"1.8rem",margin:"0 auto 1rem",boxShadow:"0 0 30px rgba(92,219,149,0.1)"}}>✓</div>
        <h2 style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.8rem",color:"#5cdb95",margin:"0 0 0.3rem"}}>Booking Confirmed!</h2>
        <p style={{color:"#4a4560",fontSize:"0.8rem",margin:0}}>Your slot is reserved. Admin has been notified. Complete Airtel Money payment to finalise.</p>
      </div>
      <div style={{background:"#13111a",border:"1px solid #2a2633",borderRadius:"12px",padding:"1rem 1.2rem",marginBottom:"0.85rem",display:"flex",justifyContent:"space-between",alignItems:"center"}}>
        <div><div style={{fontSize:"0.62rem",color:"#4a4560",textTransform:"uppercase",letterSpacing:"0.1em"}}>Booking Reference</div><div style={{fontFamily:"'Cormorant Garamond',serif",color:"#c9a96e",fontSize:"1.2rem",fontWeight:600}}>{bookingRef}</div></div>
        <button onClick={copy} style={{padding:"0.3rem 0.8rem",borderRadius:"7px",border:"1px solid #2a2633",background:"transparent",color:"#8a7f70",cursor:"pointer",fontSize:"0.72rem",fontFamily:"'DM Sans',sans-serif"}}>{copied?"✓ Copied":"Copy"}</button>
      </div>
      <div style={{background:"rgba(231,76,60,0.05)",border:"1.5px solid rgba(231,76,60,0.28)",borderRadius:"13px",padding:"1.2rem",marginBottom:"0.85rem"}}>
        <div style={{display:"flex",alignItems:"center",gap:"0.7rem",marginBottom:"1rem"}}><span style={{fontSize:"1.3rem"}}>📱</span><div><div style={{fontWeight:700,color:"#e8d5b7",fontSize:"0.88rem"}}>Pay via Airtel Money</div><div style={{fontSize:"0.68rem",color:"#5a5060"}}>Follow these steps on your phone</div></div></div>
        {[`Dial *778# on your Airtel phone`,`Select "Send Money"`,`Enter number: ${AIRTEL_NUMBER}`,`Enter amount: K${total.toLocaleString()}`,`Reference: ${bookingRef}`,`Confirm with your PIN`].map((s,i)=>(
          <div key={i} style={{display:"flex",gap:"0.6rem",alignItems:"flex-start",marginBottom:"0.45rem"}}>
            <div style={{width:"18px",height:"18px",borderRadius:"50%",background:"#c0392b",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.58rem",fontWeight:700,color:"#fff",flexShrink:0,marginTop:"0.05rem"}}>{i+1}</div>
            <span style={{fontSize:"0.78rem",color:"#a89f8c",lineHeight:1.5}}>{s}</span>
          </div>
        ))}
        <div style={{background:"rgba(201,169,110,0.08)",border:"1px solid rgba(201,169,110,0.2)",borderRadius:"9px",padding:"0.7rem",textAlign:"center",marginTop:"0.8rem"}}>
          <div style={{fontSize:"0.6rem",color:"#5a5060",textTransform:"uppercase",letterSpacing:"0.12em",marginBottom:"0.15rem"}}>Send payment to</div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.4rem",color:"#c9a96e",fontWeight:600,letterSpacing:"0.06em"}}>{AIRTEL_NUMBER}</div>
        </div>
      </div>
      <div style={{background:"rgba(92,219,149,0.05)",border:"1px solid rgba(92,219,149,0.2)",borderRadius:"12px",padding:"1rem 1.2rem",marginBottom:"1.2rem"}}>
        <div style={{display:"flex",gap:"0.7rem",alignItems:"flex-start"}}>
          <span style={{fontSize:"1rem"}}>🔔</span>
          <div>
            <div style={{fontWeight:600,color:"#5cdb95",fontSize:"0.83rem",marginBottom:"0.2rem"}}>Admin Notified in Real Time</div>
            <div style={{fontSize:"0.72rem",color:"#3a3650",lineHeight:1.5}}>Your booking just appeared live in the admin dashboard. The team can manage it there.</div>
          </div>
        </div>
      </div>
      {onSwitchAdmin&&<button onClick={onSwitchAdmin} style={{width:"100%",padding:"0.75rem",borderRadius:"9px",border:"1px solid #c9a96e44",background:"rgba(201,169,110,0.06)",color:"#c9a96e",cursor:"pointer",fontSize:"0.8rem",fontWeight:600,fontFamily:"'DM Sans',sans-serif"}}>⚙️ View in Admin Panel →</button>}

    </div>
  );
}

function ClientApp({bookings,therapists,onNewBooking,onSwitchAdmin,heroImageUrl}){
  const [step,setStep]=useState(0);
  const [cart,setCart]=useState([]);
  const [selDate,setSelDate]=useState(null);
  const [selTime,setSelTime]=useState(null);
  const [therapist,setTherapist]=useState(null);
  const [bookingRef,setBookingRef]=useState("");
  const dates=useMemo(()=>getDates(),[]);
  const total=cart.reduce((s,i)=>s+i.price,0);

  const toggleSvc=(svc)=>setCart(c=>c.some(i=>i.name===svc.name)?c.filter(i=>i.name!==svc.name):[...c,svc]);

  const handleAuth=(userData)=>{
    const ref=genRef();
    const dateStr=selDate?selDate.toISOString().slice(0,10):"";
    const mainSvc=cart[0];
    const newBooking={
      id:genId(), ref, source:"client",
      client:userData.name||userData.email.split("@")[0],
      phone:userData.phone, email:userData.email,
      service:mainSvc?.name||"", cat:mainSvc?.cat||"",
      therapist:therapist?.name||"", date:dateStr, time:selTime||"",
      amount:total, status:"pending", payment:"unpaid",
      note:`${cart.length>1?`+${cart.length-1} more service(s). `:""}Booked via app.`,
    };
    setBookingRef(ref);
    onNewBooking(newBooking);
    setStep(6);
  };

  return(
    <div style={{minHeight:"100vh",background:"#08070f",color:"#e8d5b7",fontFamily:"'DM Sans',sans-serif",paddingTop: step>0&&step<6?"100px":"50px"}}>
      {step>0&&step<6&&<div style={{position:"fixed",top:"50px",left:0,right:0,zIndex:50,background:"rgba(8,7,15,0.95)",backdropFilter:"blur(12px)"}}><ProgressBar step={step}/></div>}
      {step===0&&<HeroSection onBook={()=>setStep(1)} heroImageUrl={heroImageUrl}/>}
      {step===1&&<ServiceSelector cart={cart} toggle={toggleSvc} total={total} onNext={()=>setStep(2)}/>}
      {step===2&&<DateTimePicker dates={dates} selDate={selDate} setSelDate={setSelDate} selTime={selTime} setSelTime={setSelTime} bookings={bookings} therapistName={therapist?.name} onBack={()=>setStep(1)} onNext={()=>setStep(3)}/>}
      {step===3&&<TherapistPicker therapists={therapists} bookings={bookings} selDate={selDate} selTime={selTime} selected={therapist} setSelected={setTherapist} onBack={()=>setStep(2)} onNext={()=>setStep(4)}/>}
      {step===4&&<ReviewStep cart={cart} total={total} selDate={selDate} selTime={selTime} therapist={therapist} onBack={()=>setStep(3)} onNext={()=>setStep(5)}/>}
      {step===5&&<AuthStep total={total} onSubmit={handleAuth} onBack={()=>setStep(4)}/>}
      {step===6&&<ConfirmationStep bookingRef={bookingRef} total={total} therapist={therapist} selDate={selDate} selTime={selTime} cart={cart} onSwitchAdmin={onSwitchAdmin}/>}
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
//  ADMIN PANEL
// ═══════════════════════════════════════════════════════════════════════════

const ADMIN_NAV=[{id:"dashboard",icon:"◈",label:"Dashboard"},{id:"bookings",icon:"📋",label:"Bookings"},{id:"therapists",icon:"👥",label:"Therapists"},{id:"clients",icon:"🧑‍🤝‍🧑",label:"Clients"},{id:"revenue",icon:"📊",label:"Revenue"}];

function Sidebar({view,setView,collapsed,setCollapsed,pending}){
  return(
    <div style={{width:collapsed?"54px":"206px",background:"#0b0a10",borderRight:"1px solid #1a1823",display:"flex",flexDirection:"column",transition:"width 0.22s ease",flexShrink:0,zIndex:10}}>
      <div style={{padding:collapsed?"1rem 0":"1.1rem",borderBottom:"1px solid #1a1823",display:"flex",alignItems:"center",gap:"0.6rem",overflow:"hidden"}}>
        <span style={{fontSize:"1.2rem",flexShrink:0,display:"block",textAlign:"center",width:collapsed?"100%":"auto"}}>🌸</span>
        {!collapsed&&<div><div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.92rem",color:"#e8d5b7",fontWeight:600,whiteSpace:"nowrap"}}>Ngalula Spa</div><div style={{fontSize:"0.54rem",color:"#3a3650",letterSpacing:"0.14em",textTransform:"uppercase"}}>Admin Panel</div></div>}
      </div>
      <nav style={{flex:1,padding:"0.5rem 0"}}>
        {ADMIN_NAV.map(item=>{
          const act=view===item.id;
          return(
            <div key={item.id} onClick={()=>setView(item.id)} style={{display:"flex",alignItems:"center",gap:"0.65rem",padding:collapsed?"0.65rem 0":"0.55rem 0.9rem",cursor:"pointer",background:act?"rgba(201,169,110,0.09)":"transparent",borderRight:`2px solid ${act?"#c9a96e":"transparent"}`,justifyContent:collapsed?"center":"flex-start",position:"relative",transition:"all 0.15s"}}>
              <span style={{fontSize:"0.82rem",flexShrink:0}}>{item.icon}</span>
              {!collapsed&&<span style={{fontSize:"0.78rem",color:act?"#c9a96e":"#4a4560",fontWeight:act?600:400,whiteSpace:"nowrap"}}>{item.label}</span>}
              {item.id==="bookings"&&pending>0&&!collapsed&&<span style={{marginLeft:"auto",background:"#ff4d6d",color:"#fff",borderRadius:"10px",fontSize:"0.56rem",fontWeight:700,padding:"0.08rem 0.38rem"}}>{pending}</span>}
              {item.id==="bookings"&&pending>0&&collapsed&&<span style={{position:"absolute",top:"5px",right:"7px",width:"7px",height:"7px",borderRadius:"50%",background:"#ff4d6d"}}/>}
            </div>
          );
        })}
      </nav>
      <div onClick={()=>setCollapsed(!collapsed)} style={{padding:"0.8rem",borderTop:"1px solid #1a1823",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:collapsed?"center":"flex-end",color:"#2a2633"}}>
        <span style={{transform:collapsed?"scaleX(1)":"scaleX(-1)",display:"inline-block",fontSize:"1rem",transition:"transform 0.22s"}}>›</span>
      </div>
    </div>
  );
}

function AdminTopBar({view,unread,onBell,newBookingsCount}){
  const T={dashboard:"Dashboard",bookings:"Bookings",therapists:"Therapists",clients:"Clients",revenue:"Revenue & Analytics"};
  return(
    <div style={{height:"50px",background:"#0b0a10",borderBottom:"1px solid #1a1823",display:"flex",alignItems:"center",justifyContent:"space-between",padding:"0 1.3rem",flexShrink:0}}>
      <div style={{display:"flex",alignItems:"center",gap:"0.8rem"}}>
        <span style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7"}}>{T[view]}</span>
        {newBookingsCount>0&&<span style={{padding:"0.15rem 0.6rem",borderRadius:"20px",background:"rgba(92,219,149,0.1)",color:"#5cdb95",fontSize:"0.65rem",fontWeight:600,animation:"pulse 2s ease infinite"}}>🔴 {newBookingsCount} new live booking{newBookingsCount>1?"s":""}</span>}
      </div>
      <div style={{display:"flex",alignItems:"center",gap:"1rem"}}>
        <div onClick={onBell} style={{position:"relative",cursor:"pointer"}}>
          <span style={{fontSize:"1rem"}}>🔔</span>
          {unread>0&&<span style={{position:"absolute",top:"-2px",right:"-2px",background:"#ff4d6d",color:"#fff",borderRadius:"50%",width:"13px",height:"13px",fontSize:"0.5rem",fontWeight:700,display:"flex",alignItems:"center",justifyContent:"center"}}>{unread}</span>}
        </div>
        <div style={{display:"flex",alignItems:"center",gap:"0.45rem"}}>
          <div style={{width:"26px",height:"26px",borderRadius:"50%",background:"rgba(201,169,110,0.12)",border:"1px solid #c9a96e44",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.7rem",color:"#c9a96e",fontWeight:700}}>AD</div>
          <span style={{fontSize:"0.74rem",color:"#4a4560"}}>Admin</span>
        </div>
      </div>
    </div>
  );
}

// Booking Form
function BookingFormModal({booking,therapists,onSave,onClose}){
  const first=SERVICES_LIST[0];
  const empty={client:"",phone:"",service:first.name,cat:first.cat,therapist:therapists[0]?.name||"",date:"",time:"09:00",amount:first.price,status:"pending",payment:"unpaid",note:""};
  const [form,setForm]=useState(booking?{client:booking.client,phone:booking.phone,service:booking.service,cat:booking.cat,therapist:booking.therapist,date:booking.date,time:booking.time,amount:booking.amount,status:booking.status,payment:booking.payment,note:booking.note||""}:empty);
  const [errors,setErrors]=useState({});
  const upd=(k,v)=>setForm(f=>({...f,[k]:v}));
  const pickSvc=(n)=>{const s=SERVICES_LIST.find(x=>x.name===n);if(s)setForm(f=>({...f,service:s.name,cat:s.cat,amount:s.price}));};
  const validate=()=>{const e={};if(!form.client.trim())e.client="Required";if(!form.phone.trim())e.phone="Required";if(!form.date)e.date="Required";if(!form.amount||isNaN(+form.amount)||+form.amount<=0)e.amount="Required";setErrors(e);return!Object.keys(e).length;};
  const R2={display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.8rem"};
  return(
    <BaseModal title={booking?`Edit — ${booking.ref}`:"New Booking"} subtitle={booking?"Update booking details":"Create a new booking"} onClose={onClose} wide>
      <div style={R2}>
        <FField label="Client Name" error={errors.client}><input style={{...SI,borderColor:errors.client?"#ff4d6d55":"#2a2633"}} value={form.client} onChange={e=>upd("client",e.target.value)} placeholder="Full name"/></FField>
        <FField label="Phone" error={errors.phone}><input style={{...SI,borderColor:errors.phone?"#ff4d6d55":"#2a2633"}} value={form.phone} onChange={e=>upd("phone",e.target.value)} placeholder="+260 97 XXX"/></FField>
      </div>
      <FField label="Service"><select style={{...SI,cursor:"pointer"}} value={form.service} onChange={e=>pickSvc(e.target.value)}>{SERVICES_LIST.map(s=><option key={s.name} value={s.name}>{s.name} — K{s.price.toLocaleString()}</option>)}</select></FField>
      <div style={R2}>
        <FField label="Therapist"><select style={{...SI,cursor:"pointer"}} value={form.therapist} onChange={e=>upd("therapist",e.target.value)}>{therapists.filter(t=>t.active).map(t=><option key={t.id} value={t.name}>{t.name}</option>)}</select></FField>
        <FField label="Amount (K)" error={errors.amount}><input style={{...SI,borderColor:errors.amount?"#ff4d6d55":"#2a2633"}} type="number" min="0" value={form.amount} onChange={e=>upd("amount",e.target.value)}/></FField>
      </div>
      <div style={R2}>
        <FField label="Date" error={errors.date}><input style={{...SI,borderColor:errors.date?"#ff4d6d55":"#2a2633"}} type="date" value={form.date} onChange={e=>upd("date",e.target.value)}/></FField>
        <FField label="Time"><select style={{...SI,cursor:"pointer"}} value={form.time} onChange={e=>upd("time",e.target.value)}>{TIME_SLOTS.map(t=><option key={t} value={t}>{t}</option>)}</select></FField>
      </div>
      <div style={R2}>
        <FField label="Status"><select style={{...SI,cursor:"pointer"}} value={form.status} onChange={e=>upd("status",e.target.value)}>{Object.entries(STATUS_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select></FField>
        <FField label="Payment"><select style={{...SI,cursor:"pointer"}} value={form.payment} onChange={e=>upd("payment",e.target.value)}>{Object.entries(PAY_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select></FField>
      </div>
      <FField label="Admin Notes"><textarea style={{...SI,resize:"vertical",minHeight:"58px"}} value={form.note} onChange={e=>upd("note",e.target.value)} placeholder="Special instructions…"/></FField>
      <div style={{display:"flex",gap:"0.8rem",marginTop:"0.4rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><PrimaryBtn onClick={()=>{if(validate())onSave({...form,amount:+form.amount});}} style={{flex:2}}>{booking?"Save Changes":"Create Booking"}</PrimaryBtn></div>
    </BaseModal>
  );
}

// Therapist Form
function TherapistFormModal({therapist,onSave,onClose}){
  const empty={name:"",role:"",initials:"",color:"#c9a96e",specialties:"",phone:"",email:"",bio:"",active:true};
  const [form,setForm]=useState(therapist?{name:therapist.name,role:therapist.role,initials:therapist.initials,color:therapist.color,specialties:therapist.specialties,phone:therapist.phone||"",email:therapist.email||"",bio:therapist.bio||"",active:therapist.active}:empty);
  const [errors,setErrors]=useState({});
  const upd=(k,v)=>setForm(f=>{const nf={...f,[k]:v};if(k==="name"&&!therapist){const pts=v.trim().split(" ");nf.initials=pts.map(p=>p[0]||"").join("").toUpperCase().slice(0,2);}return nf;});
  const validate=()=>{const e={};if(!form.name.trim())e.name="Required";if(!form.role.trim())e.role="Required";if(!form.initials.trim())e.initials="Required";setErrors(e);return!Object.keys(e).length;};
  const R2={display:"grid",gridTemplateColumns:"1fr 1fr",gap:"0.8rem"};
  return(
    <BaseModal title={therapist?`Edit — ${therapist.name}`:"Add New Therapist"} subtitle="Team member details" onClose={onClose}>
      <div style={R2}>
        <FField label="Full Name" error={errors.name}><input style={{...SI,borderColor:errors.name?"#ff4d6d55":"#2a2633"}} value={form.name} onChange={e=>upd("name",e.target.value)} placeholder="e.g. Aisha Nkonde"/></FField>
        <FField label="Initials" error={errors.initials}><input style={{...SI,borderColor:errors.initials?"#ff4d6d55":"#2a2633"}} value={form.initials} onChange={e=>upd("initials",e.target.value.toUpperCase().slice(0,2))} placeholder="AN" maxLength={2}/></FField>
      </div>
      <FField label="Role" error={errors.role}><input style={{...SI,borderColor:errors.role?"#ff4d6d55":"#2a2633"}} value={form.role} onChange={e=>upd("role",e.target.value)} placeholder="e.g. Senior Massage Therapist"/></FField>
      <FField label="Specialties"><input style={SI} value={form.specialties} onChange={e=>upd("specialties",e.target.value)} placeholder="e.g. Massage, Body Treatments"/></FField>
      <div style={R2}>
        <FField label="Phone"><input style={SI} value={form.phone} onChange={e=>upd("phone",e.target.value)} placeholder="+260 97 XXX"/></FField>
        <FField label="Email"><input style={SI} type="email" value={form.email} onChange={e=>upd("email",e.target.value)} placeholder="name@spa.com"/></FField>
      </div>
      <FField label="Bio"><input style={SI} value={form.bio} onChange={e=>upd("bio",e.target.value)} placeholder="Short bio…"/></FField>
      <FField label="Profile Colour"><div style={{display:"flex",gap:"0.45rem",flexWrap:"wrap",marginTop:"0.1rem"}}>{COLORS_PRESET.map(c=><div key={c} onClick={()=>upd("color",c)} style={{width:"26px",height:"26px",borderRadius:"50%",background:c,cursor:"pointer",border:`3px solid ${form.color===c?"#fff":"transparent"}`,boxShadow:form.color===c?"0 0 0 2px #c9a96e55":"none"}}/>)}</div></FField>
      <FField label="Status"><select style={{...SI,cursor:"pointer"}} value={form.active?"active":"inactive"} onChange={e=>upd("active",e.target.value==="active")}><option value="active">Active</option><option value="inactive">Inactive</option></select></FField>
      <div style={{display:"flex",gap:"0.8rem",marginTop:"0.5rem"}}><GhostBtn onClick={onClose} style={{flex:1}}>Cancel</GhostBtn><PrimaryBtn onClick={()=>{if(validate())onSave(form);}} style={{flex:2}}>{therapist?"Save Changes":"Add Therapist"}</PrimaryBtn></div>
    </BaseModal>
  );
}

// Dashboard
function AdminDashboard({bookings,notifications,onMarkRead,heroImageUrl,setHeroImageUrl}){
  const today=new Date().toISOString().slice(0,10);
  const todayB=bookings.filter(b=>b.date===today);
  const todayRev=todayB.filter(b=>b.payment==="paid").reduce((s,b)=>s+b.amount,0);
  const pending=bookings.filter(b=>b.status==="pending").length;
  const liveBookings=bookings.filter(b=>b.source==="client");
  const CT=({active,payload,label})=>!active||!payload?.length?null:(<div style={{background:"#1a1823",border:"1px solid #2a2633",borderRadius:"8px",padding:"0.55rem 0.8rem",fontSize:"0.74rem"}}><div style={{color:"#5a5060"}}>{label}</div><div style={{color:"#c9a96e",fontWeight:600}}>K{payload[0].value.toLocaleString()}</div></div>);
  const [heroUrlInput,setHeroUrlInput]=useState(heroImageUrl);
  const updateHeroUrl=()=>setHeroImageUrl(heroUrlInput.trim());
  return(
    <div style={{padding:"1.3rem",overflowY:"auto",height:"100%"}}>
      <div style={{background:"rgba(201,169,110,0.06)",border:"1px solid rgba(201,169,110,0.15)",borderRadius:"12px",padding:"1rem 1.2rem",marginBottom:"1.2rem",display:"grid",gridTemplateColumns:"1fr 320px",gap:"1rem",alignItems:"center"}}>
        <div>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.05rem",color:"#e8d5b7",marginBottom:"0.5rem"}}>Hero Image</div>
          <div style={{fontSize:"0.77rem",color:"#4a4560",lineHeight:1.6,marginBottom:"0.85rem"}}>Change the hero section image shown to clients in the booking page. Paste any valid photo URL and save to update instantly.</div>
          <div style={{display:"flex",gap:"0.75rem",flexWrap:"wrap"}}>
            <input value={heroUrlInput} onChange={e=>setHeroUrlInput(e.target.value)} placeholder="https://..." style={{flex:1,minWidth:0,background:"#0f0e13",border:"1px solid #1e1823",borderRadius:"10px",padding:"0.75rem 0.95rem",color:"#e8d5b7",fontSize:"0.82rem",outline:"none"}}/>
            <PrimaryBtn onClick={updateHeroUrl} style={{whiteSpace:"nowrap"}}>Update Image</PrimaryBtn>
          </div>
          <div style={{fontSize:"0.68rem",color:"#5a5060",marginTop:"0.8rem"}}>Current hero image URL is used for the public booking preview only.</div>
        </div>
        <div style={{borderRadius:"14px",overflow:"hidden",border:"1px solid #1e1c26",minHeight:"160px",backgroundColor:"#0c0b11"}}>
          <img src={heroImageUrl} alt="Hero preview" style={{width:"100%",height:"100%",objectFit:"cover",display:"block"}}/>
        </div>
      </div>
      {liveBookings.length>0&&(
        <div style={{background:"rgba(92,219,149,0.06)",border:"1px solid rgba(92,219,149,0.25)",borderRadius:"12px",padding:"0.9rem 1.2rem",marginBottom:"1.2rem",display:"flex",alignItems:"center",gap:"0.8rem",animation:"slideIn 0.4s ease both"}}>
          <span style={{fontSize:"1.1rem"}}>🔴</span>
          <div style={{flex:1}}><span style={{color:"#5cdb95",fontWeight:600,fontSize:"0.85rem"}}>{liveBookings.length} live booking{liveBookings.length>1?"s":""} from app</span><span style={{color:"#3a3650",fontSize:"0.72rem",marginLeft:"0.6rem"}}>— received in real time from the booking flow</span></div>
        </div>
      )}
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(150px,1fr))",gap:"0.9rem",marginBottom:"1.2rem"}}>
        {[{i:"💰",l:"Today's Revenue",v:`K${todayRev.toLocaleString()}`,c:"#c9a96e",s:`${todayB.filter(b=>b.payment==="paid").length} paid`},{i:"📅",l:"Today's Bookings",v:todayB.length,c:"#8b9ef7",s:`${todayB.filter(b=>b.status==="confirmed").length} confirmed`},{i:"⏳",l:"Pending",v:pending,c:"#ffb347",s:"need action"},{i:"📱",l:"From App (Live)",v:liveBookings.length,c:"#5cdb95",s:"real-time"}].map(c=>(
          <div key={c.l} style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"11px",padding:"1rem",position:"relative",overflow:"hidden"}}>
            <div style={{position:"absolute",top:0,left:0,right:0,height:"2px",background:`linear-gradient(90deg,${c.c}66,transparent)`}}/>
            <div style={{fontSize:"1.1rem",marginBottom:"0.28rem"}}>{c.i}</div>
            <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.45rem",color:c.c,fontWeight:600,lineHeight:1}}>{c.v}</div>
            <div style={{fontSize:"0.62rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em",marginTop:"0.18rem"}}>{c.l}</div>
            <div style={{fontSize:"0.64rem",color:"#2a2633",marginTop:"0.1rem"}}>{c.s}</div>
          </div>
        ))}
      </div>
      <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",padding:"1.1rem",marginBottom:"1rem"}}>
        <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>Revenue — Last 14 Days</div>
        <ResponsiveContainer width="100%" height={140}>
          <BarChart data={REVENUE_DATA} barSize={14}>
            <CartesianGrid strokeDasharray="3 3" stroke="#1a1823" vertical={false}/>
            <XAxis dataKey="day" tick={{fill:"#3a3650",fontSize:8}} axisLine={false} tickLine={false} interval={1}/>
            <YAxis tick={{fill:"#3a3650",fontSize:9}} axisLine={false} tickLine={false} width={40} tickFormatter={v=>`K${(v/1000).toFixed(0)}k`}/>
            <Tooltip content={<CT/>}/>
            <Bar dataKey="rev" radius={[3,3,0,0]}>{REVENUE_DATA.map((_,i)=><Cell key={i} fill={i===REVENUE_DATA.length-1?"#c9a96e":"#2a2835"}/>)}</Bar>
          </BarChart>
        </ResponsiveContainer>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"1.3fr 1fr",gap:"1rem"}}>
        <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",padding:"1.1rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>Today's Schedule</div>
          {todayB.length===0?<p style={{color:"#2a2633",fontSize:"0.78rem"}}>No bookings today</p>:todayB.sort((a,b)=>a.time.localeCompare(b.time)).map(b=>(
            <div key={b.id} style={{display:"flex",gap:"0.7rem",alignItems:"flex-start",padding:"0.5rem 0",borderBottom:"1px solid #1a1823"}}>
              {b.source==="client"&&<span style={{width:"6px",height:"6px",borderRadius:"50%",background:"#5cdb95",marginTop:"5px",flexShrink:0}} title="Live from app"/>}
              <span style={{fontSize:"0.7rem",fontWeight:600,color:"#c9a96e",minWidth:"36px"}}>{b.time}</span>
              <div style={{flex:1,minWidth:0}}>
                <div style={{fontSize:"0.77rem",fontWeight:500,color:"#c8c0b0",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{b.client}</div>
                <div style={{fontSize:"0.64rem",color:"#3a3650",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{b.service}</div>
              </div>
              <SBadge s={b.status}/>
            </div>
          ))}
        </div>
        <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",padding:"1.1rem"}}>
          <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"0.8rem"}}>
            <span style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7"}}>Notifications</span>
            {notifications.filter(n=>!n.read).length>0&&<button onClick={onMarkRead} style={{background:"none",border:"none",fontSize:"0.65rem",color:"#c9a96e",cursor:"pointer",padding:0}}>Mark all read</button>}
          </div>
          {notifications.slice(0,6).map(n=>(
            <div key={n.id} style={{display:"flex",gap:"0.55rem",padding:"0.38rem 0",borderBottom:"1px solid #1a1823",opacity:n.read?0.42:1}}>
              <div style={{width:"6px",height:"6px",borderRadius:"50%",background:n.read?"#2a2633":n.type==="booking"?"#5cdb95":n.type==="payment"?"#c9a96e":"#ff4d6d",marginTop:"4px",flexShrink:0}}/>
              <div><div style={{fontSize:"0.7rem",color:"#8a7f70",lineHeight:1.4}}>{n.msg}</div><div style={{fontSize:"0.6rem",color:"#2a2633",marginTop:"0.1rem"}}>{n.time}</div></div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

// Bookings CRUD
function AdminBookings({bookings,setBookings,therapists}){
  const [search,setSearch]=useState("");
  const [statusF,setStatusF]=useState("all");
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const SI2={background:"#0f0e13",border:"1px solid #1e1c26",borderRadius:"8px",padding:"0.48rem 0.75rem",color:"#e8d5b7",fontSize:"0.77rem",outline:"none",fontFamily:"'DM Sans',sans-serif"};
  const filtered=useMemo(()=>bookings.filter(b=>(statusF==="all"||b.status===statusF)&&(b.client.toLowerCase().includes(search.toLowerCase())||b.ref.toLowerCase().includes(search.toLowerCase())||b.service.toLowerCase().includes(search.toLowerCase()))).sort((a,b)=>a.date.localeCompare(b.date)||a.time.localeCompare(b.time)),[bookings,search,statusF]);
  const save=(fd)=>{if(editing)setBookings(bb=>bb.map(b=>b.id===editing.id?{...b,...fd}:b));else setBookings(bb=>[...bb,{...fd,id:genId(),ref:genRef(),source:"admin"}]);setShowForm(false);setEditing(null);};
  const del=(id)=>{setBookings(bb=>bb.filter(b=>b.id!==id));setDeleting(null);};
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto",display:"flex",flexDirection:"column",gap:"0.9rem"}}>
      <div style={{display:"flex",gap:"0.6rem",flexWrap:"wrap",alignItems:"center"}}>
        <input placeholder="Search client, ref, service…" value={search} onChange={e=>setSearch(e.target.value)} style={{...SI2,flex:"1 1 160px"}}/>
        <select value={statusF} onChange={e=>setStatusF(e.target.value)} style={{...SI2,cursor:"pointer"}}><option value="all">All Statuses</option>{Object.entries(STATUS_META).map(([k,v])=><option key={k} value={k}>{v.l}</option>)}</select>
        <span style={{fontSize:"0.68rem",color:"#2a2633"}}>{filtered.length} shown</span>
        <PrimaryBtn onClick={()=>{setEditing(null);setShowForm(true);}} style={{marginLeft:"auto",whiteSpace:"nowrap",padding:"0.5rem 1.1rem"}}>+ New Booking</PrimaryBtn>
      </div>
      <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",overflow:"hidden"}}>
        <div style={{overflowX:"auto"}}>
          <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.77rem"}}>
            <thead><tr style={{borderBottom:"1px solid #1e1c26"}}>{["Src","Ref","Client","Service","Therapist","Date/Time","Amount","Status","Payment","Actions"].map(h=><th key={h} style={{padding:"0.68rem 0.8rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.61rem",textTransform:"uppercase",letterSpacing:"0.1em",whiteSpace:"nowrap"}}>{h}</th>)}</tr></thead>
            <tbody>
              {filtered.length===0?<tr><td colSpan={10} style={{textAlign:"center",padding:"3rem",color:"#2a2633"}}>No bookings match</td></tr>:filtered.map((b,i)=>(
                <tr key={b.id} style={{borderBottom:"1px solid #111019",background:b.source==="client"?"rgba(92,219,149,0.025)":i%2?"rgba(255,255,255,0.01)":"transparent"}}
                  onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.04)"}
                  onMouseLeave={e=>e.currentTarget.style.background=b.source==="client"?"rgba(92,219,149,0.025)":i%2?"rgba(255,255,255,0.01)":"transparent"}
                >
                  <td style={{padding:"0.58rem 0.8rem"}}><span title={b.source==="client"?"Live from app":"Admin entry"} style={{fontSize:"0.75rem"}}>{b.source==="client"?"🟢":"⚙️"}</span></td>
                  <td style={{padding:"0.58rem 0.8rem",color:"#c9a96e",fontFamily:"monospace",fontSize:"0.68rem"}}>{b.ref}</td>
                  <td style={{padding:"0.58rem 0.8rem"}}><div style={{fontWeight:500,color:"#c8c0b0",whiteSpace:"nowrap"}}>{b.client}</div><div style={{fontSize:"0.61rem",color:"#2a2633"}}>{b.phone}</div></td>
                  <td style={{padding:"0.58rem 0.8rem",color:"#6e6460",maxWidth:"130px",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{b.service}</td>
                  <td style={{padding:"0.58rem 0.8rem",color:"#5a5060",fontSize:"0.72rem",whiteSpace:"nowrap"}}>{b.therapist?.split(" ")[0]}</td>
                  <td style={{padding:"0.58rem 0.8rem",whiteSpace:"nowrap"}}><div style={{color:"#8a7f70",fontSize:"0.72rem"}}>{b.date}</div><div style={{color:"#c9a96e",fontWeight:600,fontSize:"0.75rem"}}>{b.time}</div></td>
                  <td style={{padding:"0.58rem 0.8rem",color:"#c9a96e",fontWeight:700,whiteSpace:"nowrap"}}>K{b.amount?.toLocaleString()}</td>
                  <td style={{padding:"0.58rem 0.8rem"}}><SBadge s={b.status}/></td>
                  <td style={{padding:"0.58rem 0.8rem"}}><PBadge s={b.payment}/></td>
                  <td style={{padding:"0.58rem 0.8rem"}}>
                    <div style={{display:"flex",gap:"0.28rem",flexWrap:"wrap"}}>
                      {b.status==="pending"&&<button onClick={()=>setBookings(bb=>bb.map(x=>x.id===b.id?{...x,status:"confirmed"}:x))} style={{padding:"0.16rem 0.42rem",borderRadius:"5px",border:"1px solid #5cdb9533",background:"transparent",color:"#5cdb95",cursor:"pointer",fontSize:"0.6rem"}}>✓</button>}
                      {b.payment==="unpaid"&&b.status!=="cancelled"&&<button onClick={()=>setBookings(bb=>bb.map(x=>x.id===b.id?{...x,payment:"paid"}:x))} style={{padding:"0.16rem 0.42rem",borderRadius:"5px",border:"1px solid #c9a96e33",background:"transparent",color:"#c9a96e",cursor:"pointer",fontSize:"0.6rem"}}>$</button>}
                      <button onClick={()=>{setEditing(b);setShowForm(true);}} style={{padding:"0.16rem 0.42rem",borderRadius:"5px",border:"1px solid #2a2633",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.6rem"}}>✏</button>
                      <button onClick={()=>setDeleting(b)} style={{padding:"0.16rem 0.42rem",borderRadius:"5px",border:"1px solid #ff4d6d22",background:"transparent",color:"#ff4d6d",cursor:"pointer",fontSize:"0.6rem"}}>🗑</button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <div style={{padding:"0.5rem 0.8rem",borderTop:"1px solid #1e1c26",fontSize:"0.65rem",color:"#2a2633",display:"flex",justifyContent:"space-between"}}>
          <span>{filtered.length} of {bookings.length} · 🟢 = live from app</span>
          <span>Total: K{filtered.reduce((s,b)=>s+(b.amount||0),0).toLocaleString()}</span>
        </div>
      </div>
      {showForm&&<BookingFormModal booking={editing} therapists={therapists} onSave={save} onClose={()=>{setShowForm(false);setEditing(null);}}/>}
      {deleting&&<DeleteConfirm what="booking" name={`${deleting.ref} — ${deleting.client}`} onConfirm={()=>del(deleting.id)} onClose={()=>setDeleting(null)}/>}
    </div>
  );
}

// Therapists CRUD
function AdminTherapists({therapists,setTherapists,bookings}){
  const [showForm,setShowForm]=useState(false);
  const [editing,setEditing]=useState(null);
  const [deleting,setDeleting]=useState(null);
  const save=(fd)=>{if(editing)setTherapists(tt=>tt.map(t=>t.id===editing.id?{...t,...fd}:t));else setTherapists(tt=>[...tt,{...fd,id:genId(),sessions:0,rating:5.0,revenue:0,active:true}]);setShowForm(false);setEditing(null);};
  const del=(id)=>{setTherapists(tt=>tt.filter(t=>t.id!==id));setDeleting(null);};
  const today=new Date().toISOString().slice(0,10);
  return(
    <div style={{padding:"1.3rem",overflowY:"auto",height:"100%"}}>
      <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:"1.2rem"}}>
        <div><h3 style={{margin:0,fontFamily:"'Cormorant Garamond',serif",fontSize:"1.1rem",color:"#e8d5b7"}}>Therapist Management</h3><p style={{margin:"0.15rem 0 0",fontSize:"0.7rem",color:"#3a3650"}}>{therapists.filter(t=>t.active).length} active · {therapists.length} total — changes reflect instantly in client booking</p></div>
        <PrimaryBtn onClick={()=>{setEditing(null);setShowForm(true);}}>+ Add Therapist</PrimaryBtn>
      </div>
      <div style={{background:"rgba(92,219,149,0.04)",border:"1px solid rgba(92,219,149,0.15)",borderRadius:"10px",padding:"0.7rem 1rem",marginBottom:"1.2rem",fontSize:"0.75rem",color:"#5cdb95"}}>
        ⚡ Real-time sync: therapists added or deactivated here are immediately reflected in the client booking flow.
      </div>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fill,minmax(280px,1fr))",gap:"1rem"}}>
        {therapists.map(t=>{
          const tB=bookings.filter(b=>b.therapist===t.name);
          const todayB=tB.filter(b=>b.date===today);
          return(
            <div key={t.id} style={{background:"#13111a",border:`1px solid ${t.color}33`,borderRadius:"13px",padding:"1.2rem",position:"relative",overflow:"hidden",opacity:t.active?1:0.5}}>
              <div style={{position:"absolute",top:0,left:0,right:0,height:"3px",background:`linear-gradient(90deg,${t.color},transparent)`}}/>
              <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:"0.9rem"}}>
                <div style={{display:"flex",gap:"0.75rem",alignItems:"center"}}>
                  <div style={{width:"44px",height:"44px",borderRadius:"50%",background:`${t.color}15`,border:`2px solid ${t.color}44`,display:"flex",alignItems:"center",justifyContent:"center",fontFamily:"'Cormorant Garamond',serif",fontSize:"0.88rem",color:t.color,fontWeight:700,flexShrink:0}}>{t.initials}</div>
                  <div><div style={{fontWeight:600,color:"#e8d5b7",fontSize:"0.86rem"}}>{t.name}</div><div style={{fontSize:"0.67rem",color:t.color,marginTop:"0.06rem"}}>{t.role}</div></div>
                </div>
                <div style={{display:"flex",flexDirection:"column",gap:"0.28rem",alignItems:"flex-end"}}>
                  <button onClick={()=>setTherapists(tt=>tt.map(x=>x.id===t.id?{...x,active:!x.active}:x))} style={{padding:"0.15rem 0.5rem",borderRadius:"20px",border:`1px solid ${t.active?"#5cdb9544":"#ff4d6d44"}`,background:"transparent",color:t.active?"#5cdb95":"#ff4d6d",cursor:"pointer",fontSize:"0.6rem",fontWeight:600}}>{t.active?"Active":"Inactive"}</button>
                  <div style={{display:"flex",gap:"0.28rem"}}>
                    <button onClick={()=>{setEditing(t);setShowForm(true);}} style={{padding:"0.15rem 0.45rem",borderRadius:"5px",border:"1px solid #2a2633",background:"transparent",color:"#8b9ef7",cursor:"pointer",fontSize:"0.62rem"}}>✏ Edit</button>
                    <button onClick={()=>setDeleting(t)} style={{padding:"0.15rem 0.45rem",borderRadius:"5px",border:"1px solid #ff4d6d33",background:"transparent",color:"#ff4d6d",cursor:"pointer",fontSize:"0.62rem"}}>🗑</button>
                  </div>
                </div>
              </div>
              <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:"0.4rem",marginBottom:"0.8rem"}}>
                {[{l:"Sessions",v:t.sessions},{l:"Rating",v:`${t.rating}⭐`},{l:"Revenue",v:`K${(t.revenue/1000).toFixed(0)}k`}].map(s=>(
                  <div key={s.l} style={{textAlign:"center",background:"rgba(255,255,255,0.03)",borderRadius:"7px",padding:"0.42rem 0.2rem"}}>
                    <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.88rem",color:t.color,fontWeight:600}}>{s.v}</div>
                    <div style={{fontSize:"0.57rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.07em"}}>{s.l}</div>
                  </div>
                ))}
              </div>
              {(t.phone||t.email)&&<div style={{background:"rgba(255,255,255,0.02)",borderRadius:"7px",padding:"0.48rem 0.6rem",marginBottom:"0.7rem",fontSize:"0.66rem"}}>{t.phone&&<div style={{color:"#5a5060",marginBottom:"0.12rem"}}>📞 {t.phone}</div>}{t.email&&<div style={{color:"#5a5060"}}>✉ {t.email}</div>}</div>}
              <div style={{fontSize:"0.62rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.1em",marginBottom:"0.45rem"}}>Today ({todayB.length})</div>
              {todayB.length===0?<div style={{fontSize:"0.7rem",color:"#2a2633",fontStyle:"italic"}}>No bookings today</div>:todayB.map(b=>(
                <div key={b.id} style={{display:"flex",gap:"0.5rem",alignItems:"center",padding:"0.32rem 0",borderTop:"1px solid #1a1823"}}>
                  {b.source==="client"&&<span style={{width:"5px",height:"5px",borderRadius:"50%",background:"#5cdb95",flexShrink:0}} title="From app"/>}
                  <span style={{color:t.color,fontSize:"0.7rem",fontWeight:600,minWidth:"36px"}}>{b.time}</span>
                  <div style={{flex:1,minWidth:0}}><div style={{fontSize:"0.7rem",color:"#8a7f70",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{b.client}</div></div>
                  <SBadge s={b.status}/>
                </div>
              ))}
              <div style={{marginTop:"0.6rem",paddingTop:"0.5rem",borderTop:"1px solid #1a1823",fontSize:"0.64rem",color:"#2a2633"}}>{tB.length} total on record</div>
            </div>
          );
        })}
      </div>
      {showForm&&<TherapistFormModal therapist={editing} onSave={save} onClose={()=>{setShowForm(false);setEditing(null);}}/>}
      {deleting&&<DeleteConfirm what="therapist" name={deleting.name} onConfirm={()=>del(deleting.id)} onClose={()=>setDeleting(null)}/>}
    </div>
  );
}

function AdminClients({bookings}){
  const clients=useMemo(()=>{const map={};bookings.forEach(b=>{if(!map[b.client])map[b.client]={name:b.client,phone:b.phone,visits:0,spent:0,last:"",bookings:[]};map[b.client].visits++;if(b.payment==="paid")map[b.client].spent+=b.amount||0;if(!map[b.client].last||b.date>map[b.client].last)map[b.client].last=b.date;map[b.client].bookings.push(b);});return Object.values(map).sort((a,b)=>b.spent-a.spent);},[bookings]);
  return(
    <div style={{padding:"1.3rem",height:"100%",overflowY:"auto"}}>
      <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",overflow:"hidden"}}>
        <div style={{overflowX:"auto"}}>
          <table style={{width:"100%",borderCollapse:"collapse",fontSize:"0.77rem"}}>
            <thead><tr style={{borderBottom:"1px solid #1e1c26"}}>{["#","Client","Phone","Bookings","Paid","Last Visit","History"].map(h=><th key={h} style={{padding:"0.68rem 0.8rem",textAlign:"left",color:"#3a3650",fontWeight:500,fontSize:"0.61rem",textTransform:"uppercase",letterSpacing:"0.1em",whiteSpace:"nowrap"}}>{h}</th>)}</tr></thead>
            <tbody>
              {clients.map((c,i)=>{
                const tier=c.spent>8000?{l:"VIP",col:"#c9a96e"}:c.spent>3000?{l:"Regular",col:"#5cdb95"}:{l:"New",col:"#8b9ef7"};
                const hasLive=c.bookings.some(b=>b.source==="client");
                return(
                  <tr key={c.name} style={{borderBottom:"1px solid #111019",background:hasLive?"rgba(92,219,149,0.02)":i%2?"rgba(255,255,255,0.01)":"transparent"}}
                    onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.04)"}
                    onMouseLeave={e=>e.currentTarget.style.background=hasLive?"rgba(92,219,149,0.02)":i%2?"rgba(255,255,255,0.01)":"transparent"}
                  >
                    <td style={{padding:"0.58rem 0.8rem",color:"#2a2633",fontSize:"0.68rem"}}>{i+1}</td>
                    <td style={{padding:"0.58rem 0.8rem"}}><div style={{display:"flex",alignItems:"center",gap:"0.55rem"}}><div style={{width:"24px",height:"24px",borderRadius:"50%",background:"rgba(201,169,110,0.1)",display:"flex",alignItems:"center",justifyContent:"center",fontSize:"0.6rem",color:"#c9a96e",fontWeight:700,flexShrink:0}}>{c.name.split(" ").map(w=>w[0]).join("").slice(0,2)}</div><div><div style={{fontWeight:500,color:"#c8c0b0",whiteSpace:"nowrap"}}>{c.name}{hasLive&&<span style={{marginLeft:"0.4rem",fontSize:"0.58rem",color:"#5cdb95"}}>🟢</span>}</div><span style={{fontSize:"0.57rem",padding:"0.05rem 0.36rem",borderRadius:"10px",background:`${tier.col}15`,color:tier.col,fontWeight:600}}>{tier.l}</span></div></div></td>
                    <td style={{padding:"0.58rem 0.8rem",fontSize:"0.72rem",color:"#5a5060"}}>{c.phone}</td>
                    <td style={{padding:"0.58rem 0.8rem",color:"#8b9ef7",fontWeight:600,textAlign:"center"}}>{c.visits}</td>
                    <td style={{padding:"0.58rem 0.8rem",color:"#c9a96e",fontWeight:700}}>K{c.spent.toLocaleString()}</td>
                    <td style={{padding:"0.58rem 0.8rem",color:"#4a4560",fontSize:"0.72rem",whiteSpace:"nowrap"}}>{c.last}</td>
                    <td style={{padding:"0.58rem 0.8rem"}}><div style={{display:"flex",gap:"0.18rem"}}>{c.bookings.slice(0,6).map(b=><div key={b.id} style={{width:"7px",height:"7px",borderRadius:"2px",background:STATUS_META[b.status]?.c||"#2a2633"}} title={b.status}/>)}{c.bookings.length>6&&<span style={{fontSize:"0.58rem",color:"#2a2633"}}>+{c.bookings.length-6}</span>}</div></td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
        <div style={{padding:"0.5rem 0.8rem",borderTop:"1px solid #1e1c26",fontSize:"0.64rem",color:"#2a2633",display:"flex",justifyContent:"space-between"}}><span>{clients.length} unique clients · 🟢 = booked via app</span><span>Lifetime paid: K{clients.reduce((s,c)=>s+c.spent,0).toLocaleString()}</span></div>
      </div>
    </div>
  );
}

function AdminRevenue({bookings}){
  const total=REVENUE_DATA.reduce((s,d)=>s+d.rev,0);
  const CT=({active,payload,label})=>!active||!payload?.length?null:(<div style={{background:"#1a1823",border:"1px solid #2a2633",borderRadius:"8px",padding:"0.55rem 0.8rem",fontSize:"0.74rem"}}><div style={{color:"#5a5060"}}>{label}</div><div style={{color:"#c9a96e",fontWeight:600}}>K{payload[0].value.toLocaleString()}</div></div>);
  const cats=[{n:"Massage",rev:14200,c:"#c9a96e"},{n:"Pedicures",rev:7850,c:"#5cdb95"},{n:"Facials",rev:6100,c:"#d4a8ff"},{n:"Manicure",rev:2800,c:"#ff9eb5"}];
  return(
    <div style={{padding:"1.3rem",overflowY:"auto",height:"100%",display:"flex",flexDirection:"column",gap:"1rem"}}>
      <div style={{display:"grid",gridTemplateColumns:"repeat(auto-fit,minmax(135px,1fr))",gap:"0.9rem"}}>
        {[{i:"💰",l:"14-Day Revenue",v:`K${total.toLocaleString()}`,c:"#c9a96e"},{i:"📅",l:"Total Bookings",v:bookings.length,c:"#8b9ef7"},{i:"✅",l:"Completed",v:bookings.filter(b=>b.status==="completed").length,c:"#5cdb95"},{i:"📱",l:"Via App",v:bookings.filter(b=>b.source==="client").length,c:"#d4a8ff"}].map(c=>(
          <div key={c.l} style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"11px",padding:"0.95rem",position:"relative",overflow:"hidden"}}>
            <div style={{position:"absolute",top:0,left:0,right:0,height:"2px",background:`linear-gradient(90deg,${c.c}66,transparent)`}}/>
            <div style={{fontSize:"1rem",marginBottom:"0.25rem"}}>{c.i}</div>
            <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"1.35rem",color:c.c,fontWeight:600}}>{c.v}</div>
            <div style={{fontSize:"0.61rem",color:"#3a3650",textTransform:"uppercase",letterSpacing:"0.08em"}}>{c.l}</div>
          </div>
        ))}
      </div>
      <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",padding:"1.1rem"}}>
        <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>Daily Revenue — May 2026</div>
        <ResponsiveContainer width="100%" height={170}>
          <BarChart data={REVENUE_DATA} barSize={14}>
            <CartesianGrid strokeDasharray="3 3" stroke="#1a1823" vertical={false}/>
            <XAxis dataKey="day" tick={{fill:"#3a3650",fontSize:8}} axisLine={false} tickLine={false}/>
            <YAxis tick={{fill:"#3a3650",fontSize:9}} axisLine={false} tickLine={false} width={40} tickFormatter={v=>`K${(v/1000).toFixed(0)}k`}/>
            <Tooltip content={<CT/>}/>
            <Bar dataKey="rev" radius={[3,3,0,0]}>{REVENUE_DATA.map((_,i)=><Cell key={i} fill={i===REVENUE_DATA.length-1?"#c9a96e":"#2a2835"}/>)}</Bar>
          </BarChart>
        </ResponsiveContainer>
      </div>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:"1rem"}}>
        <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",padding:"1.1rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>By Category</div>
          {cats.map(c=><div key={c.n} style={{marginBottom:"0.8rem"}}><div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",marginBottom:"0.22rem"}}><span style={{color:"#6e6460"}}>{c.n}</span><span style={{color:c.c,fontWeight:600}}>K{c.rev.toLocaleString()}</span></div><div style={{background:"#0f0e13",borderRadius:"4px",height:"5px"}}><div style={{width:`${Math.round((c.rev/cats[0].rev)*100)}%`,height:"100%",background:`linear-gradient(90deg,${c.c}88,${c.c})`,borderRadius:"4px"}}/></div></div>)}
        </div>
        <div style={{background:"#13111a",border:"1px solid #1e1c26",borderRadius:"12px",padding:"1.1rem"}}>
          <div style={{fontFamily:"'Cormorant Garamond',serif",fontSize:"0.95rem",color:"#e8d5b7",marginBottom:"0.9rem"}}>Payment Status</div>
          {(()=>{const paid=bookings.filter(b=>b.payment==="paid").reduce((s,b)=>s+(b.amount||0),0);const unpaid=bookings.filter(b=>b.payment==="unpaid"&&b.status!=="cancelled").reduce((s,b)=>s+(b.amount||0),0);return[{l:"Collected",v:paid,c:"#5cdb95"},{l:"Outstanding",v:unpaid,c:"#ff4d6d"}].map(r=><div key={r.l} style={{marginBottom:"0.85rem"}}><div style={{display:"flex",justifyContent:"space-between",fontSize:"0.72rem",marginBottom:"0.22rem"}}><span style={{color:"#6e6460"}}>{r.l}</span><span style={{color:r.c,fontWeight:600}}>K{r.v.toLocaleString()}</span></div><div style={{background:"#0f0e13",borderRadius:"4px",height:"5px"}}><div style={{width:`${Math.round((r.v/(paid+unpaid||1))*100)}%`,height:"100%",background:r.c,borderRadius:"4px"}}/></div></div>);})()}
        </div>
      </div>
    </div>
  );
}

function NotifPanel({notifications,setNotifications,onClose}){
  return(
    <div style={{position:"fixed",top:"100px",right:"12px",width:"296px",background:"#13111a",border:"1px solid #2a2633",borderRadius:"13px",boxShadow:"0 20px 60px rgba(0,0,0,0.6)",zIndex:150,overflow:"hidden",animation:"slideIn 0.25s ease both"}}>
      <div style={{padding:"0.85rem 1.1rem",borderBottom:"1px solid #1e1c26",display:"flex",justifyContent:"space-between",alignItems:"center"}}>
        <span style={{fontFamily:"'Cormorant Garamond',serif",color:"#e8d5b7",fontSize:"0.92rem"}}>Notifications</span>
        <div style={{display:"flex",gap:"0.7rem",alignItems:"center"}}>
          <button onClick={()=>setNotifications(ns=>ns.map(n=>({...n,read:true})))} style={{background:"none",border:"none",fontSize:"0.65rem",color:"#c9a96e",cursor:"pointer",padding:0}}>Mark all read</button>
          <button onClick={onClose} style={{background:"none",border:"none",color:"#5a5060",fontSize:"1.2rem",cursor:"pointer",lineHeight:1}}>×</button>
        </div>
      </div>
      <div style={{maxHeight:"340px",overflowY:"auto"}}>
        {notifications.map(n=>(
          <div key={n.id} onClick={()=>setNotifications(ns=>ns.map(x=>x.id===n.id?{...x,read:true}:x))} style={{display:"flex",gap:"0.6rem",padding:"0.75rem 1.1rem",borderBottom:"1px solid #111019",background:n.read?"transparent":"rgba(201,169,110,0.04)",cursor:"pointer"}}
            onMouseEnter={e=>e.currentTarget.style.background="rgba(201,169,110,0.06)"}
            onMouseLeave={e=>e.currentTarget.style.background=n.read?"transparent":"rgba(201,169,110,0.04)"}
          >
            <div style={{width:"7px",height:"7px",borderRadius:"50%",background:n.read?"#2a2633":n.type==="booking"?"#5cdb95":n.type==="payment"?"#c9a96e":"#ff4d6d",marginTop:"4px",flexShrink:0}}/>
            <div><div style={{fontSize:"0.74rem",color:n.read?"#4a4560":"#a89f8c",lineHeight:1.4}}>{n.msg}</div><div style={{fontSize:"0.61rem",color:"#2a2633",marginTop:"0.12rem"}}>{n.time}</div></div>
          </div>
        ))}
      </div>
    </div>
  );
}

function AdminApp({bookings,setBookings,therapists,setTherapists,notifications,setNotifications,newBookingsCount,heroImageUrl,setHeroImageUrl}){
  const [view,setView]=useState("dashboard");
  const [collapsed,setCollapsed]=useState(false);
  const [showNotifs,setShowNotifs]=useState(false);
  const unread=notifications.filter(n=>!n.read).length;
  const pending=bookings.filter(b=>b.status==="pending").length;
  return(
    <div style={{height:"100%",display:"flex",flexDirection:"column",background:"#08070f",color:"#e8d5b7",fontFamily:"'DM Sans',sans-serif"}}>
      <div style={{display:"flex",flex:1,overflow:"visible"}}>
        <Sidebar view={view} setView={v=>{setView(v);setShowNotifs(false);}} collapsed={collapsed} setCollapsed={setCollapsed} pending={pending}/>
        <div style={{flex:1,display:"flex",flexDirection:"column",overflow:"visible"}}>
          <AdminTopBar view={view} unread={unread} onBell={()=>setShowNotifs(s=>!s)} newBookingsCount={newBookingsCount}/>
          <div style={{flex:1,overflowY:"auto"}}>
            {view==="dashboard"  &&<AdminDashboard   bookings={bookings} notifications={notifications} onMarkRead={()=>setNotifications(ns=>ns.map(n=>({...n,read:true})))} heroImageUrl={heroImageUrl} setHeroImageUrl={setHeroImageUrl}/>}
            {view==="bookings"   &&<AdminBookings    bookings={bookings} setBookings={setBookings} therapists={therapists}/>}
            {view==="therapists" &&<AdminTherapists  therapists={therapists} setTherapists={setTherapists} bookings={bookings}/>}
            {view==="clients"    &&<AdminClients     bookings={bookings}/>}
            {view==="revenue"    &&<AdminRevenue     bookings={bookings}/>}
          </div>
        </div>
      </div>
      {showNotifs&&<><NotifPanel notifications={notifications} setNotifications={setNotifications} onClose={()=>setShowNotifs(false)}/><div onClick={()=>setShowNotifs(false)} style={{position:"fixed",inset:0,zIndex:140}}/></>}
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
//  ROOT — SHARED STATE BRIDGE
// ═══════════════════════════════════════════════════════════════════════════
export default function NgalulaUnifiedApp(){
  const [mode,setMode]=useState("client");
  const [bookings,setBookings]=useState(SEED_BOOKINGS);
  const [therapists,setTherapists]=useState(SEED_THERAPISTS);
  const [heroImageUrl,setHeroImageUrl]=useState("https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=1600&q=80");
  const [notifications,setNotifications]=useState([
    {id:1,type:"booking",  msg:"New booking — Natasha Phiri · Couples' Massage",time:"13:00",read:false},
    {id:2,type:"payment",  msg:"Payment received — Yvonne Musonda · K1,100",    time:"12:30",read:false},
    {id:3,type:"booking",  msg:"New booking — Mutale Kabwe · Deep Cleanse Facial",time:"11:00",read:true},
  ]);
  const [newBookingsCount,setNewBookingsCount]=useState(0);
  const [pulseAdmin,setPulseAdmin]=useState(false);
  const [secretClicks,setSecretClicks]=useState(0);
  const [lastSecretClick,setLastSecretClick]=useState(Date.now());
  const [showAdminToast,setShowAdminToast]=useState(false);
  const [toastMessage,setToastMessage]=useState("");

  const handleNewBooking=useCallback((booking)=>{
    setBookings(bb=>[booking,...bb]);
    setNotifications(ns=>[{
      id:genId(),
      type:"booking",
      msg:`🟢 LIVE: ${booking.client} booked ${booking.service} — K${booking.amount.toLocaleString()}`,
      time:"Just now",
      read:false
    },...ns]);
    setNewBookingsCount(c=>c+1);
    setPulseAdmin(true);
    setTimeout(()=>setPulseAdmin(false),5000);
  },[]);

  useEffect(()=>{
    if(secretClicks < 1) return;
    const message = secretClicks < 5 ? `Hidden access ${secretClicks}/5` : (mode === "client" ? "Admin access unlocked" : "Returned to client booking");
    setToastMessage(message);
    setShowAdminToast(true);
    if(secretClicks >= 5){
      setMode(m=>m==="client"?"admin":"client");
      setSecretClicks(0);
    }
    const timer = setTimeout(()=>setShowAdminToast(false),2000);
    return ()=>clearTimeout(timer);
  },[secretClicks,mode]);

  useEffect(()=>{
    const onKeyDown = (e) => {
      if(e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'a'){
        setMode(m=>m==="client"?"admin":"client");
        setToastMessage(prev=>prev.includes("Admin")?"Returned to client booking":"Admin access unlocked");
        setShowAdminToast(true);
        const timer = setTimeout(()=>setShowAdminToast(false),2000);
        return ()=>clearTimeout(timer);
      }
    };
    window.addEventListener('keydown', onKeyDown);
    return ()=>window.removeEventListener('keydown', onKeyDown);
  },[]);

  const handleSecretAdminClick=()=>{
    const now = Date.now();
    if(now - lastSecretClick > 4000){
      setSecretClicks(1);
    } else {
      setSecretClicks(c=>c+1);
    }
    setLastSecretClick(now);
  };

  return(
    <div style={{height:"100vh",overflow:"auto",background:"#08070f",fontFamily:"'DM Sans',sans-serif",position:"relative"}}>
      <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
      <div onClick={handleSecretAdminClick} style={{position:"fixed",top:0,right:0,width:"90px",height:"90px",zIndex:700,opacity:0,cursor:"pointer",pointerEvents:"auto"}} title="Hidden admin access" />
      {showAdminToast && (
        <div style={{position:"fixed",top:"18px",right:"18px",background:"rgba(0,0,0,0.8)",color:"#e8d5b7",padding:"0.7rem 1rem",borderRadius:"12px",fontSize:"0.8rem",zIndex:750,boxShadow:"0 16px 40px rgba(0,0,0,0.35)"}}>
          {toastMessage}
        </div>
      )}
      <div style={{height:"100%",paddingTop:"50px",boxSizing:"border-box"}}>
        {mode==="client" && <ClientApp bookings={bookings} therapists={therapists} onNewBooking={handleNewBooking} heroImageUrl={heroImageUrl}/>}
        {mode==="admin" && <AdminApp bookings={bookings} setBookings={setBookings} therapists={therapists} setTherapists={setTherapists} notifications={notifications} setNotifications={setNotifications} newBookingsCount={newBookingsCount} heroImageUrl={heroImageUrl} setHeroImageUrl={setHeroImageUrl}/>}
      </div>
    </div>
  );
}
