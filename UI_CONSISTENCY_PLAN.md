# UI Consistency & Search Enhancement Plan

## Issues Identified

### 1. Search Functionality
**Current Problem:**
- Search only works with `name` and `code`
- IMEI numbers are NOT searchable
- Multiple search fields confuse users

**Required Fix:**
- Single unified search field
- Search by: Name, Code, IMEI, Brand, Model
- Real-time search results

### 2. UI Consistency
**Current Problem:**
- Items page has extra fields (category, brand, model, IMEI, warranty)
- Base repo has simpler UI
- Font consistency issues on transaction pages

**Required Fix:**
- Keep enhanced features but match base repo styling
- Consistent fonts across all pages
- Clean, minimal UI

### 3. Transaction Page Fonts
**Current Problem:**
- Different fonts on transaction pages
- Inconsistent with rest of app

**Required Fix:**
- Use monospace font consistently (as defined in main.css)
- Match base repo styling

## Implementation Plan

### Phase 1: Enhanced Search (Priority: HIGH)
1. Update `Item` model `itemsearch()` method to include IMEI
2. Update `Search` controller to handle unified search
3. Update items.js to use single search field
4. Add real-time search with debouncing

### Phase 2: UI Simplification (Priority: MEDIUM)
1. Keep current features but simplify layout
2. Match base repo's clean design
3. Remove unnecessary visual clutter
4. Consistent spacing and alignment

### Phase 3: Font Consistency (Priority: MEDIUM)
1. Ensure all pages use monospace font
2. Check transaction pages specifically
3. Verify print styles

## Files to Modify

### Backend
- `application/models/Item.php` - Enhanced search method
- `application/controllers/Search.php` - Unified search handler

### Frontend
- `application/views/items/items.php` - Simplified UI
- `public/js/items.js` - Enhanced search logic
- `public/css/main.css` - Font consistency

### Database
- No schema changes needed (IMEI already in `item_serials` table)

## Search Logic

### Unified Search Query
```sql
SELECT DISTINCT items.* 
FROM items
LEFT JOIN item_serials ON items.id = item_serials.item_id
WHERE 
    items.name LIKE '%search%' OR
    items.code LIKE '%search%' OR
    items.brand LIKE '%search%' OR
    items.model LIKE '%search%' OR
    item_serials.imei_number LIKE '%search%'
```

## UI Mockup

### Items Page Header (Simplified)
```
[Add New Item]  Show: [10▼] per page  Category: [All▼]  Sort by: [Name A-Z▼]  🔍 [Search...]
```

### Search Behavior
- Type in search box
- Searches: Name, Code, IMEI, Brand, Model
- Results update in real-time (300ms debounce)
- Shows matched items below
- Highlights matching field

## Benefits

1. **Better UX**: Single search field, easier to use
2. **More Powerful**: Search by IMEI, brand, model
3. **Consistent**: Matches base repo styling
4. **Professional**: Clean, minimal design
5. **Fast**: Real-time search with debouncing

## Next Steps

1. Implement enhanced search in Item model
2. Update Search controller
3. Modify items view for cleaner UI
4. Update JavaScript for real-time search
5. Test thoroughly
6. Apply same pattern to other pages
