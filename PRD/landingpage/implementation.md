# ScentCents Landing Page Implementation

> **Document Type**: Complete Landing Page Specification  
> **Created**: January 4, 2026  
> **Based on**: [Landing Page Research Guide](./research.md)

---

## Product Context

| Attribute | Value |
|-----------|-------|
| **Product** | ScentCents - Perfume Price Comparison Platform |
| **Target Audience** | Perfume enthusiasts seeking best prices on authentic fragrances |
| **Primary Conversion Goal** | Email signup for price alerts |
| **Secondary Goal** | Browse/search perfumes |

### Key Pain Points
1. **Time waste** — Hours comparing prices across 10+ websites
2. **Missing deals** — Flash sales gone before you know they existed
3. **Trust anxiety** — Fear of buying fakes from unknown sellers
4. **Overpaying** — Not knowing a better price exists elsewhere

### Core Value Proposition
> "Compare perfume prices from 50+ verified sellers—including Reddit's most trusted fragrance sellers. Track prices. Get alerts. Save money."

### Key Differentiators
1. **Reddit seller integration** — Only platform that searches community-vetted Reddit sellers
2. **Verified seller network** — Every seller authenticated for authenticity
3. **Price intelligence** — Historical charts reveal if "sales" are real
4. **Zero cost** — 100% free, no premium tiers

---

## Section 1: Hero Section (Above the Fold)

### Purpose
Capture attention in 8 seconds. Communicate value. Drive first action.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│  [ScentCents Logo]                    [Get Price Alerts] button │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│        H1: Stop Overpaying for Perfumes                         │
│                                                                 │
│        P: Compare prices from 50+ verified sellers—including    │
│           Reddit's most trusted fragrance sellers.              │
│           Track prices. Get alerts. Save money.                 │
│                                                                 │
│        ┌─────────────────────────────────────┬────────────────┐ │
│        │ 🔍 Search any perfume...            │ Find Prices →  │ │
│        └─────────────────────────────────────┴────────────────┘ │
│                                                                 │
│        ✓ 12,000+ Users   ✓ 53 Sellers   ✓ ₹24L+ Saved          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Navigation Bar
```
[ScentCents Logo]                              [Get Price Alerts →]
```
- **Logo**: ScentCents wordmark with subtle fragrance visual element
- **CTA Button**: Teal background (#0D9488), white text, 14px font weight 600

#### Headline (H1)
```
Stop Overpaying for Perfumes
```
- **Font**: 48px desktop / 32px mobile, font-weight 700
- **Color**: Near-black (#1C1917)
- **Max-width**: 600px (prevent line break on key words)

#### Subheadline
```
Compare prices from 50+ verified sellers—including Reddit's most trusted 
fragrance sellers. Track prices. Get alerts. Save money.
```
- **Font**: 20px desktop / 18px mobile, font-weight 400
- **Color**: Warm gray (#57534E)
- **Max-width**: 640px
- **Line-height**: 1.6

#### Search Box
```
┌──────────────────────────────────────────────┬───────────────────┐
│ 🔍 Search any perfume... (e.g., Dior Sauvage)│  Find Prices →    │
└──────────────────────────────────────────────┴───────────────────┘
```
- **Input**: 56px height, 18px font, subtle shadow
- **Placeholder**: "Search any perfume... (e.g., Dior Sauvage)"
- **Button**: Teal gradient, "Find Prices →", 56px height
- **Border-radius**: 12px for container
- **Shadow**: 0 4px 20px rgba(0,0,0,0.08)

#### Trust Bar
```
✓ 12,000+ Users    ✓ 53 Verified Sellers    ✓ ₹24,00,000+ Saved
```
- **Layout**: Horizontal on desktop, 3 columns on mobile
- **Font**: 14px, font-weight 500
- **Icons**: Checkmarks in teal (#0D9488)
- **Spacing**: 32px between items

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | Subtle gradient: cream (#FFFBEB) to white |
| **Hero Image** | None—focus on search box as primary visual |
| **Decorative Elements** | Subtle perfume bottle silhouettes at 5% opacity in corners |
| **Section Height** | 90vh minimum (fills viewport) |

### Implementation Notes

> [!IMPORTANT]
> - Search box must be functional—not just visual. On submit, redirect to `/perfumes?q={query}`
> - Trust bar numbers should be dynamic (pull from database or update monthly)
> - No navigation links except logo and single CTA—maintain 1:1 attention ratio

---

## Section 2: Problem/Agitation Section

### Purpose
Create recognition. Make users feel understood. Amplify the pain they're already experiencing.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│        H2: Tired of the Perfume Price Hunt?                     │
│        P: We've all been there. It shouldn't be this hard.      │
│                                                                 │
│   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│   │     🔍       │  │     🚨       │  │     ❓       │         │
│   │  Endless     │  │  Missing     │  │   Trust      │         │
│   │  Tab         │  │  the         │  │   Issues     │         │
│   │  Switching   │  │  Sales       │  │              │         │
│   │              │  │              │  │              │         │
│   │  "Jumping    │  │  "Flash      │  │  "Is this    │         │
│   │  between     │  │  deals gone  │  │  seller      │         │
│   │  10+ sites"  │  │  before you  │  │  legit?"     │         │
│   │              │  │  know"       │  │              │         │
│   └──────────────┘  └──────────────┘  └──────────────┘         │
│                                                                 │
│   ┌──────────────────────────────────────────────────────┐     │
│   │                        💸                             │     │
│   │              Overpaying Without Knowing               │     │
│   │                                                       │     │
│   │   "Paid ₹8,000 when another seller had it for ₹5,500" │     │
│   └──────────────────────────────────────────────────────┘     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Section Headline (H2)
```
Tired of the Perfume Price Hunt?
```
- **Font**: 40px desktop / 28px mobile, font-weight 700
- **Color**: Near-black (#1C1917)
- **Text-align**: Center

#### Section Subheadline
```
We've all been there. It shouldn't be this hard.
```
- **Font**: 18px, font-weight 400
- **Color**: Warm gray (#57534E)
- **Margin-bottom**: 48px

#### Pain Point Cards

**Card 1: Endless Tab Switching**
```
🔍

Endless Tab Switching

Jumping between 10+ websites just to find who has the best price. 
Copy-paste. Compare. Repeat. There goes your evening.
```

**Card 2: Missing the Sales**
```
🚨

Missing the Sales

Flash deals. Limited-time offers. Gone before you even knew they existed. 
And you paid full price two days later.
```

**Card 3: Trust Issues**
```
❓

Seller Trust Issues

"Is this seller legit? Will I get an authentic bottle or a knockoff?" 
The anxiety is real—especially with unfamiliar sellers.
```

**Card 4: Overpaying Without Knowing** (Featured/Wider)
```
💸

Overpaying Without Knowing

Paid ₹8,000 for that bottle? Another seller had it for ₹5,500. 
But how would you know? You can't check everywhere.
```

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | Light gray (#F5F5F4) |
| **Card Background** | White with subtle shadow |
| **Card Border-radius** | 16px |
| **Icons** | 48px emoji or custom icons |
| **Card Layout** | 3-column grid, with 4th card spanning full width below |
| **Padding** | 32px inside cards |

### Implementation Notes

> [!TIP]
> - Icons can be emoji (as shown) or custom SVG icons for more polish
> - Fourth card (Overpaying) should be visually emphasized—it's the strongest pain point
> - Consider subtle animation on scroll (fade-up) for each card

---

## Section 3: Solution/Benefits Section

### Purpose
Position ScentCents as the answer. Shift from problem to solution. Focus on outcomes, not features.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│        H2: One Search. Every Seller. Best Price.                │
│        P: ScentCents does the hard work so you don't have to.   │
│                                                                 │
│   ┌─────────────────────────────┐  ┌─────────────────────────┐  │
│   │ 📊                          │  │ 🔔                       │  │
│   │ Compare in Seconds          │  │ Price Drop Alerts        │  │
│   │                             │  │                          │  │
│   │ See prices from 50+         │  │ Set your target price.   │  │
│   │ sellers instantly.          │  │ We notify you when       │  │
│   │ No more tab juggling.       │  │ it drops.                │  │
│   │                             │  │                          │  │
│   │ → Save 3+ hours             │  │ → Average user saves     │  │
│   │   per purchase              │  │   ₹2,400/year            │  │
│   └─────────────────────────────┘  └─────────────────────────┘  │
│                                                                 │
│   ┌─────────────────────────────┐  ┌─────────────────────────┐  │
│   │ ✅                          │  │ 📈                       │  │
│   │ Verified Sellers Only       │  │ Price History            │  │
│   │                             │  │                          │  │
│   │ Every seller is vetted.     │  │ See if today's "sale"    │  │
│   │ Reddit sellers verified     │  │ is actually a good deal. │  │
│   │ by community reputation.    │  │ Never fall for fake      │  │
│   │                             │  │ discounts.               │  │
│   │ → Buy without the           │  │                          │  │
│   │   fake fragrance anxiety    │  │ → Confidence in every    │  │
│   │                             │  │   purchase               │  │
│   └─────────────────────────────┘  └─────────────────────────┘  │
│                                                                 │
│              [Start Comparing Prices →] button                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Section Headline (H2)
```
One Search. Every Seller. Best Price.
```
- **Font**: 40px desktop / 28px mobile, font-weight 700
- **Color**: Near-black (#1C1917)
- **Text-align**: Center

#### Section Subheadline
```
ScentCents does the hard work so you don't have to.
```
- **Font**: 18px, font-weight 400
- **Color**: Warm gray (#57534E)
- **Margin-bottom**: 48px

#### Benefit Cards

**Card 1: Compare in Seconds**
```
📊

Compare in Seconds

See prices from 50+ sellers instantly. Official retailers AND trusted 
Reddit sellers—all in one place. No more tab juggling.

→ Save 3+ hours per purchase
```

**Card 2: Price Drop Alerts**
```
🔔

Price Drop Alerts

Set your target price. We'll notify you the moment it drops. 
Buy when you're ready, at the price you want.

→ Average user saves ₹2,400/year
```

**Card 3: Verified Sellers Only**
```
✅

Verified Sellers Only

Every seller is vetted for authenticity. Reddit sellers verified 
by community reputation and transaction history.

→ Buy without the fake fragrance anxiety
```

**Card 4: Price History**
```
📈

Price History

See if today's "sale" is actually a good deal. Track prices over 
time and never fall for fake discounts again.

→ Confidence in every purchase
```

#### CTA Button
```
Start Comparing Prices →
```
- **Style**: Primary teal button, centered
- **Size**: 56px height, 240px min-width
- **Margin-top**: 48px

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | White |
| **Card Background** | Subtle cream (#FFFBEB) or light teal (#F0FDFA) |
| **Card Border** | 1px solid rgba(0,0,0,0.06) |
| **Outcome Text** | Teal color (#0D9488), prepended with arrow |
| **Card Layout** | 2x2 grid on desktop, 1 column on mobile |
| **Icon Size** | 40px |

### Implementation Notes

> [!NOTE]
> - Each card follows the Feature → Benefit → Outcome structure from research
> - "Outcome" line (→ Save 3+ hours) should be visually distinct—teal text or highlighted background
> - CTA links to `/perfumes` page or opens signup modal

---

## Section 4: Social Proof & Trust Section

### Purpose
Build credibility. Answer "Can I trust this?" Leverage the success of others.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│   ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌───────────┐ │
│   │  12,437     │ │ ₹24,00,000+ │ │     53      │ │   2,340   │ │
│   │  Active     │ │  Saved      │ │  Verified   │ │ Perfumes  │ │
│   │  Users      │ │  This Month │ │  Sellers    │ │ Tracked   │ │
│   └─────────────┘ └─────────────┘ └─────────────┘ └───────────┘ │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ┌────────────────────────────────────────────────────────┐    │
│   │ ★★★★★                                                  │    │
│   │                                                        │    │
│   │ "I was about to pay ₹8,500 for Dior Sauvage.           │    │
│   │  ScentCents showed me a Reddit seller with the same    │    │
│   │  bottle for ₹5,200. Verified, authentic, and I saved   │    │
│   │  ₹3,300 on one purchase."                              │    │
│   │                                                        │    │
│   │  [Photo] Rahul M. • Mumbai • Saved ₹12K+ in 6 months   │    │
│   └────────────────────────────────────────────────────────┘    │
│                                                                 │
│   ┌────────────────────────────────────────────────────────┐    │
│   │ ★★★★★                                                  │    │
│   │                                                        │    │
│   │ "The price alerts are a game-changer. Set one for      │    │
│   │  Bleu de Chanel, got notified 2 weeks later when it    │    │
│   │  dropped 30%. This app pays for itself (and it's       │    │
│   │  free!)."                                              │    │
│   │                                                        │    │
│   │  [Photo] Priya S. • Bangalore • 8 alerts active        │    │
│   └────────────────────────────────────────────────────────┘    │
│                                                                 │
│   ┌────────────────────────────────────────────────────────┐    │
│   │ ★★★★★                                                  │    │
│   │                                                        │    │
│   │ "Finally, a site that actually includes Reddit         │    │
│   │  sellers! That's where all the good decant deals are.  │    │
│   │  Been using it for 3 months—game changer."             │    │
│   │                                                        │    │
│   │  [Photo] Vikram T. • Delhi • Member since Oct 2025     │    │
│   └────────────────────────────────────────────────────────┘    │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│        Trusted by the fragrance community:                      │
│                                                                 │
│   [r/desifragranceaddicts]  [r/fragranceswap]  [r/Perfumes]    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Statistics Bar

| Stat | Number | Label |
|------|--------|-------|
| Users | **12,437** | Active Users |
| Savings | **₹24,00,000+** | Saved This Month |
| Sellers | **53** | Verified Sellers |
| Perfumes | **2,340** | Perfumes Tracked |

- **Number Font**: 32px, font-weight 700, near-black
- **Label Font**: 14px, font-weight 500, warm gray
- **Layout**: 4-column on desktop, 2x2 on mobile

#### Testimonial 1
```
★★★★★

"I was about to pay ₹8,500 for Dior Sauvage. ScentCents showed me a Reddit 
seller with the same bottle for ₹5,200. Verified, authentic, and I saved 
₹3,300 on one purchase."

[Avatar] Rahul M.
Mumbai • Saved ₹12K+ in 6 months
```

#### Testimonial 2
```
★★★★★

"The price alerts are a game-changer. Set one for Bleu de Chanel, got 
notified 2 weeks later when it dropped 30%. This app pays for itself 
(and it's free!)."

[Avatar] Priya S.
Bangalore • 8 alerts active
```

#### Testimonial 3
```
★★★★★

"Finally, a site that actually includes Reddit sellers! That's where all 
the good decant deals are. Been using it for 3 months—game changer."

[Avatar] Vikram T.
Delhi • Member since Oct 2025
```

#### Community Trust Line
```
Trusted by the fragrance community:

r/desifragranceaddicts  •  r/fragranceswap  •  r/Perfumes
```

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | Light gray (#F5F5F4) |
| **Stats Bar Background** | White strip with shadow |
| **Testimonial Cards** | White, 16px border-radius, subtle shadow |
| **Star Color** | Gold/amber (#F59E0B) |
| **Avatar Size** | 48px circle |
| **Reddit Badges** | Styled like Reddit community pills |

### Implementation Notes

> [!IMPORTANT]
> - Stats should be real and updated regularly (or use conservative estimates)
> - Testimonials should be real users—consider reaching out to early users for quotes
> - If you don't have real testimonials yet, use "Early feedback from beta users:" as intro

---

## Section 5: How It Works / Differentiation Section

### Purpose
Reduce confusion. Make the process feel simple. Highlight what makes ScentCents unique.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│        H2: How ScentCents Works                                 │
│        P: From search to savings in 30 seconds.                 │
│                                                                 │
│   ┌────────────┐       ┌────────────┐       ┌────────────┐      │
│   │     ①      │  ───▶ │     ②      │  ───▶ │     ③      │      │
│   │            │       │            │       │            │      │
│   │  [Visual]  │       │  [Visual]  │       │  [Visual]  │      │
│   │            │       │            │       │            │      │
│   │   Search   │       │  Compare   │       │  Save or   │      │
│   │    Any     │       │   Prices   │       │   Alert    │      │
│   │  Perfume   │       │            │       │            │      │
│   │            │       │            │       │            │      │
│   │ Type any   │       │ See all    │       │ Buy now or │      │
│   │ fragrance  │       │ prices     │       │ set an     │      │
│   │ name. We   │       │ side by    │       │ alert for  │      │
│   │ search 50+ │       │ side.      │       │ when it    │      │
│   │ sellers.   │       │ Filter by  │       │ drops.     │      │
│   │            │       │ size or    │       │            │      │
│   │            │       │ seller.    │       │            │      │
│   └────────────┘       └────────────┘       └────────────┘      │
│                                                                 │
│                                                                 │
│        ┌───────────────────────────────────────────────────┐    │
│        │                                                   │    │
│        │  💡 What makes us different?                      │    │
│        │                                                   │    │
│        │  Unlike Google or other comparison sites,         │    │
│        │  ScentCents is the ONLY platform that searches    │    │
│        │  trusted Reddit sellers—where the best decant     │    │
│        │  and authentic fragrance deals actually are.      │    │
│        │                                                   │    │
│        └───────────────────────────────────────────────────┘    │
│                                                                 │
│              [Start Comparing Prices →] button                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Section Headline (H2)
```
How ScentCents Works
```

#### Section Subheadline
```
From search to savings in 30 seconds.
```

#### Step 1
```
①

Search Any Perfume

Type any fragrance name. We instantly search 50+ sellers—official 
retailers AND community-verified Reddit sellers.
```

#### Step 2
```
②

Compare Prices

See all prices side by side. Filter by size, seller type, or stock 
status. Spot the best deal immediately.
```

#### Step 3
```
③

Save or Set Alert

Found a deal? Go buy it. Not ready? Set a price alert and we'll 
notify you the moment it drops to your target.
```

#### Differentiator Callout Box
```
💡 What makes us different?

Unlike Google or other comparison sites, ScentCents is the ONLY platform 
that searches trusted Reddit sellers—where the best decant and authentic 
fragrance deals actually are.
```

#### CTA Button
```
Start Comparing Prices →
```

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | White |
| **Step Numbers** | Large circles with step number, teal background |
| **Step Visuals** | Simple illustrations or product UI mockups |
| **Connector Arrows** | Subtle dotted lines or arrows between steps |
| **Callout Box** | Teal-tinted background (#F0FDFA), teal left border |
| **Layout** | Horizontal on desktop, vertical on mobile |

### Implementation Notes

> [!TIP]
> - Consider using actual UI screenshots for step visuals (more credible)
> - The "What makes us different?" box is crucial—this is the key differentiator
> - Keep steps to exactly 3—human brains process 3 easily

---

## Section 6: FAQ Section (Objection Handling)

### Purpose
Address concerns. Remove friction. Answer the questions blocking signup.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│        H2: Questions? We've Got Answers.                        │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ Q: Is ScentCents really free?                           │   │
│   │                                                         │   │
│   │ A: Yes, 100% free. No premium tiers, no hidden fees.    │   │
│   │    We earn a small commission when you click through    │   │
│   │    to a seller and make a purchase—you never pay        │   │
│   │    extra.                                               │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ Q: Are Reddit sellers safe to buy from?                 │   │
│   │                                                         │   │
│   │ A: We only list Reddit sellers with established         │   │
│   │    reputations, verified transaction history, and       │   │
│   │    community vouches. Look for our "Community           │   │
│   │    Verified" badge. We never list unvetted sellers.     │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ Q: How often are prices updated?                        │   │
│   │                                                         │   │
│   │ A: Official retailer prices refresh every 4 hours.      │   │
│   │    Reddit seller prices update when they publish new    │   │
│   │    inventory—typically weekly. We timestamp everything  │   │
│   │    so you know how fresh the data is.                   │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ Q: Will you spam me with emails?                        │   │
│   │                                                         │   │
│   │ A: Never. We only send emails when YOUR price alerts    │   │
│   │    trigger. No newsletters. No promotions. No spam.     │   │
│   │    Just the deals you specifically asked for.           │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ Q: What if a seller doesn't have what I want?           │   │
│   │                                                         │   │
│   │ A: Set a price alert! We'll automatically monitor all   │   │
│   │    sellers and notify you when your perfume becomes     │   │
│   │    available or hits your target price.                 │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Section Headline (H2)
```
Questions? We've Got Answers.
```

#### FAQ Items

**Q1: Is ScentCents really free?**
```
Yes, 100% free. No premium tiers, no hidden fees. We earn a small 
commission when you click through to a seller and make a purchase—you 
never pay extra.
```

**Q2: Are Reddit sellers safe to buy from?**
```
We only list Reddit sellers with established reputations, verified 
transaction history, and community vouches. Look for our "Community 
Verified" ✓ badge. We never list unvetted sellers.
```

**Q3: How often are prices updated?**
```
Official retailer prices refresh every 4 hours. Reddit seller prices 
update when they publish new inventory—typically weekly. We timestamp 
everything so you know how fresh the data is.
```

**Q4: Will you spam me with emails?**
```
Never. We only send emails when YOUR price alerts trigger. No newsletters. 
No promotions. No spam. Just the deals you specifically asked for.
```

**Q5: What if a seller doesn't have what I want?**
```
Set a price alert! We'll automatically monitor all sellers and notify 
you when your perfume becomes available or hits your target price.
```

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | Light gray (#F5F5F4) |
| **FAQ Cards** | White background, subtle shadow |
| **Question Text** | 18px, font-weight 600, near-black |
| **Answer Text** | 16px, font-weight 400, warm gray |
| **Layout** | Single column, cards stacked |
| **Accordion** | Optional—can use expandable format on mobile |

### Implementation Notes

> [!NOTE]
> - These FAQs directly address the top objections from research
> - Order matters—most important objections (free, trust) come first
> - Consider accordion behavior on mobile to save space

---

## Section 7: Final CTA Section

### Purpose
Convert. This is the last chance before they leave. Make it compelling.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│           H2: Ready to Stop Overpaying?                         │
│                                                                 │
│           P: Join 12,000+ fragrance enthusiasts who             │
│              never miss a deal.                                 │
│                                                                 │
│        ┌─────────────────────────────────┬───────────────────┐  │
│        │ Enter your email address...     │ Get Price Alerts →│  │
│        └─────────────────────────────────┴───────────────────┘  │
│                                                                 │
│           ✓ No spam. Only price drops you care about.           │
│           ✓ Unsubscribe anytime with one click.                 │
│                                                                 │
│        ─────────────────────────────────────────────────────    │
│                                                                 │
│           🔥 143 price alerts triggered in the last 24 hours    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Section Headline (H2)
```
Ready to Stop Overpaying?
```
- **Font**: 40px desktop / 28px mobile, font-weight 700
- **Color**: White (section has dark background)

#### Section Subheadline
```
Join 12,000+ fragrance enthusiasts who never miss a deal.
```
- **Font**: 18px, font-weight 400
- **Color**: White with 90% opacity

#### Email Form
```
┌─────────────────────────────────────────┬─────────────────────┐
│ Enter your email address...             │  Get Price Alerts → │
└─────────────────────────────────────────┴─────────────────────┘
```
- **Input**: White background, 56px height
- **Button**: Gold/amber (#F59E0B), dark text, 56px height
- **Border-radius**: 12px
- **Placeholder**: "Enter your email address..."

#### Reassurance Text
```
✓ No spam. Only price drops you care about.
✓ Unsubscribe anytime with one click.
```
- **Font**: 14px, font-weight 400
- **Color**: White with 70% opacity
- **Icons**: Checkmarks in white

#### Urgency Element (Ethical)
```
🔥 143 price alerts triggered in the last 24 hours
```
- **Style**: Pill/badge with subtle pulse animation on the fire emoji
- **Font**: 14px, font-weight 500
- **Background**: White with 10% opacity
- **Note**: Number should be real and dynamic

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | Teal gradient: #0D9488 to #0F766E |
| **Section Height** | Generous padding, ~400px |
| **Form Width** | Max 600px, centered |
| **Decorative Elements** | Subtle perfume bottle silhouettes at low opacity |

### Implementation Notes

> [!CAUTION]
> - Form must submit to email signup flow (likely creates account with just email)
> - Show success message inline: "You're in! Check your inbox to confirm."
> - The urgency stat ("143 alerts triggered") should be real—pull from database

---

## Section 8: Footer (Minimal)

### Purpose
Legal compliance. Secondary navigation. Don't distract from conversion.

### Layout Specification

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│   [ScentCents Logo]                                             │
│                                                                 │
│   The smarter way to find perfume deals.                        │
│                                                                 │
│   ─────────────────────────────────────────────────────         │
│                                                                 │
│   Privacy Policy  •  Terms of Service  •  Contact               │
│                                                                 │
│   © 2026 ScentCents. Made with 💜 for fragrance enthusiasts.    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Copy

#### Tagline
```
The smarter way to find perfume deals.
```

#### Links
```
Privacy Policy  •  Terms of Service  •  Contact
```

#### Copyright
```
© 2026 ScentCents. Made with 💜 for fragrance enthusiasts.
```

### Visual Elements

| Element | Specification |
|---------|--------------|
| **Background** | Near-black (#1C1917) |
| **Logo** | White version of ScentCents logo |
| **Text Color** | White with 60% opacity for body, 80% for links |
| **Padding** | 48px vertical |

### Implementation Notes

> [!IMPORTANT]
> - Privacy Policy and Terms of Service pages MUST exist before launch (legal requirement)
> - Contact can be a mailto link initially, or link to a simple contact form
> - Keep footer minimal—don't add social links, blog links, etc. that distract

---

## Global Implementation Notes

### Performance Requirements

| Metric | Target |
|--------|--------|
| **LCP** (Largest Contentful Paint) | < 2.5 seconds |
| **FID** (First Input Delay) | < 100ms |
| **CLS** (Cumulative Layout Shift) | < 0.1 |
| **Total Page Size** | < 1.5MB |

### Responsive Breakpoints

| Breakpoint | Width | Key Changes |
|------------|-------|-------------|
| Mobile | < 640px | Single column, larger touch targets (56px) |
| Tablet | 640-1024px | 2-column grids |
| Desktop | > 1024px | Full layouts as shown |

### Typography Stack

```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 
             Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
```

### Color Palette

| Name | Hex | Usage |
|------|-----|-------|
| Primary Teal | #0D9488 | CTAs, accents, links |
| Dark Teal | #0F766E | Hover states, gradients |
| Gold/Amber | #F59E0B | Secondary CTAs, highlights, savings |
| Near Black | #1C1917 | Headlines, body text |
| Warm Gray | #57534E | Subheadlines, secondary text |
| Light Gray | #F5F5F4 | Section backgrounds |
| Cream | #FFFBEB | Card backgrounds, subtle warmth |
| Success Green | #10B981 | "You save" indicators |

### Accessibility Requirements

- All images have alt text
- Color contrast ratio ≥ 4.5:1 for body text
- All interactive elements keyboard accessible
- Focus states visible on all inputs/buttons
- Semantic HTML structure (proper heading hierarchy)

---

## A/B Testing Priorities (Post-Launch)

| Priority | Test | Variants |
|----------|------|----------|
| 🔴 P0 | Hero headline | "Stop Overpaying" vs "Compare Prices from 50+ Sellers" |
| 🔴 P0 | CTA copy | "Get Price Alerts" vs "Start Saving Now" |
| 🟡 P1 | Form placement | In hero vs after benefits section |
| 🟡 P1 | Social proof position | Above the fold vs after problem section |

---

## Checklist Before Launch

### Content
- [ ] All copy finalized and proofread
- [ ] Real testimonials collected (or placeholder strategy defined)
- [ ] Stats are accurate and have a source
- [ ] Privacy Policy page exists
- [ ] Terms of Service page exists

### Technical
- [ ] Search form functional and connected to backend
- [ ] Email signup form connected to email service
- [ ] All CTAs have correct links
- [ ] Page speed under 3 seconds
- [ ] Mobile tested on real devices
- [ ] Analytics installed (GA4 or equivalent)
- [ ] Heatmap tool installed (Hotjar, Clarity, etc.)

### Design
- [ ] All images optimized (WebP format, proper sizes)
- [ ] Favicon and social share image set
- [ ] Consistent spacing and typography
- [ ] Dark mode (optional, but consider for v2)

---

*Implementation Specification Complete*  
*Document Version: 1.0*  
*Last Updated: January 4, 2026*
