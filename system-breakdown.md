# TreeO System Specification

## 1. System Overview

Seed-Emma is a comprehensive system for managing seedling procurement, distribution, and monitoring. The system facilitates tracking of seedlings from procurement through distribution to farmers and communities, and monitors their growth and development.

## 2. Data Model

### 2.1 Core Entities

#### Users
- `id`: Primary key
- `name`: User's full name
- `email`: Unique email for authentication
- `password`: Hashed password
- `role`: Enum (Admin, Supervisor, Donor, Farmer)
- `phone`: Contact number
- `status`: Account status
- `last_login`: Timestamp
- `created_at`, `updated_at`: Timestamps

#### Farmers
- `id`: Primary key
- `user_id`: Foreign key to Users (optional, if farmer has account)
- `name`: Farmer's name
- `phone`: Contact number
- `email`: Email address (optional)
- `district`: Geographic district
- `sub_county`: Sub-county
- `parish`: Parish
- `village`: Village
- `latitude`, `longitude`: GPS coordinates
- `status`: Active/Inactive
- `created_at`, `updated_at`: Timestamps

#### Communities
- `id`: Primary key
- `name`: Community name
- `leader_name`: Community leader
- `contact`: Contact information
- `district`, `sub_county`, `parish`, `village`: Location hierarchy
- `latitude`, `longitude`: GPS coordinates
- `member_count`: Number of members
- `created_at`, `updated_at`: Timestamps

#### Seedlings
- `id`: Primary key
- `name`: Seedling name
- `type`: Seedling type/category
- `description`: Detailed description
- `growth_stages`: JSON array of growth stages and expected timelines
- `image_url`: Image of the seedling
- `created_at`, `updated_at`: Timestamps

#### Inventory
- `id`: Primary key
- `seedling_id`: Foreign key to Seedlings
- `quantity`: Current quantity
- `unit_price`: Price per unit
- `batch_number`: Batch identifier
- `procurement_date`: Date procured
- `expiry_date`: Expiration date (if applicable)
- `status`: In stock/Reserved/Distributed
- `created_at`, `updated_at`: Timestamps

#### Distributions
- `id`: Primary key
- `recipient_type`: Enum (Farmer/Community)
- `recipient_id`: Foreign key to Farmers or Communities
- `seedling_id`: Foreign key to Seedlings
- `quantity`: Number distributed
- `distribution_date`: Date of distribution
- `distributor_id`: User who performed distribution
- `notes`: Additional notes
- `status`: Distribution status
- `created_at`, `updated_at`: Timestamps

#### Monitoring
- `id`: Primary key
- `distribution_id`: Foreign key to Distributions
- `growth_stage`: Current growth stage
- `observation_date`: Date of observation
- `supervisor_id`: User who performed monitoring
- `notes`: Observation notes
- `image_urls`: JSON array of image URLs
- `latitude`, `longitude`: GPS coordinates of monitoring
- `environmental_conditions`: JSON with weather data
- `created_at`, `updated_at`: Timestamps

### 2.2 Relationships
- User (1) → (0..n) Monitoring Records
- Farmer (1) → (0..n) Distributions
- Community (1) → (0..n) Distributions
- Seedling (1) → (0..n) Inventory Items
- Seedling (1) → (0..n) Distributions
- Distribution (1) → (0..n) Monitoring Records

## 3. System Modules

### 3.1 User Management Module

#### Components
- User registration and authentication
- Role-based access control
- User profile management
- Activity logging
- Password reset functionality
- Two-factor authentication (future)

#### Implementation Details
- Use Filament for authentication and user management
- Implement Filament Shield for role-based access control
- Create user profile pages with Filament resources
- Set up activity logging using Filament activity log plugin

### 3.2 Procurement Process Module

#### 3.2.1 Procurement Workflow
1. **Requisition**
   - Staff creates purchase requisition
   - Specifies items, quantities, and required delivery date
   - Attaches supporting documents if needed
   - Submits for approval

2. **Approval**
   - System routes requisition to appropriate approvers
   - Email notifications sent to approvers
   - Multi-level approval based on amount
   - Approval/rejection with comments

3. **Vendor Selection**
   - System suggests approved vendors based on item category
   - Price comparison from multiple vendors
   - Vendor performance history review
   - Selection and documentation of vendor choice

4. **Purchase Order Generation**
   - Automatic PO number generation
   - Standardized PO template
   - Terms and conditions application
   - Digital signature capture

5. **Goods Receipt**
   - QR code scanning of received items
   - Quality inspection documentation
   - Quantity verification
   - Damage/shortage reporting

6. **Invoice Processing**
   - 3-way matching (PO, GRN, Invoice)
   - Discrepancy resolution workflow
   - Payment scheduling
   - Document archiving

#### 3.2.2 Supplier Management
- **Supplier Registration**
  - Company information
  - Product catalogs
  - Certifications and compliance documents
  - Performance metrics tracking

- **Supplier Evaluation**
  - Quality rating system
  - Delivery performance tracking
  - Pricing analysis
  - Contract management

#### 3.2.3 Seedling-Specific Procurement
- **Nursery Management**
  - Nursery registration and certification
  - Seedling quality standards
  - Growth tracking from propagation
  - Batch and lot tracking

- **Quality Control**
  - Germination rate testing
  - Disease inspection
  - Growth stage verification
  - Rejection and return process

#### 3.2.4 Implementation Details
- **Filament Resources**
  - Purchase Requisition management
  - Vendor/Supplier directory
  - Purchase Order processing
  - Goods Receipt Notes
  - Invoice matching and approval

- **Workflow Automation**
  - Status tracking with Filament state management
  - Automated notifications using Filament Notifications
  - Document generation with PDF exports
  - Integration with inventory management

- **Reporting**
  - Procurement cycle time analysis
  - Spend analysis by category/vendor
  - Price variance reporting
  - Supplier performance dashboards

- **Integration Points**
  - Inventory module for stock levels
  - Financial system for budget checks
  - Supplier portals for order tracking
  - Mobile app for goods receipt

#### 3.2.5 Key Features
- **Automated Reordering**
  - Minimum/maximum stock levels
  - Seasonal demand forecasting
  - Lead time calculations
  - Suggested order quantities

- **Compliance**
  - Audit trail of all transactions
  - Document version control
  - Approval hierarchy enforcement
  - Digital signature requirements

- **Mobile Functionality**
  - Barcode/QR code scanning
  - Field receipt of goods
  - Real-time status updates
  - Photo documentation

### 3.3 Distribution Management Module

#### Components
- Farmer/community registration
- Seedling assignment
- Distribution tracking
- QR/barcode scanning
- Distribution reporting

#### Implementation Details
- Create Filament resources for farmer/community registration
- Implement distribution allocation system using Filament relations
- Set up QR code generation and scanning with Filament actions
- Design distribution reports with Filament tables and filters

### 3.4 Monitoring and Growth Tracking Module

#### Components
- Growth stage framework
- Monitoring data collection
- Image and notes upload
- Offline data collection
- GPS tagging
- Environmental data tracking

#### Implementation Details
- Define growth stage models for different seedling types as Filament resources
- Create monitoring forms with image upload using Filament forms
- Implement offline storage with Filament PWA capabilities
- Set up GPS location capture using Filament fields
- Integrate with weather APIs using Filament API integrations

### 3.5 Map Visualization Module

#### Components
- Leaflet.js integration
- Marker clustering
- Information popups
- Filtering capabilities
- Public map view
- Timeline visualization

#### Implementation Details
- Set up Leaflet integration within Filament panels
- Implement clustering algorithm for grouping nearby points
- Create custom popups with seedling information using Filament infolist
- Design filter controls for map data using Filament filters
- Set up public routes with restricted data access using Filament's authorization
- Implement timeline slider for historical view as a custom Filament widget

### 3.6 Dashboard Module

#### Components
- User-specific dashboards
- Interactive charts and statistics
- Real-time alerts and notifications
- Customizable widgets
- Data export functionality

#### Implementation Details
- Create role-specific dashboard layouts using Filament panels
- Implement charts using Filament widgets with ApexCharts integration
- Set up real-time notifications with Filament notifications
- Design widget system using Filament's built-in widget capabilities
- Implement data export in multiple formats with Filament exports

### 3.7 Reporting Module

#### Components
- Standard report templates
- Custom report builder
- Filtering and parameter selection
- Export functionality
- Scheduled report delivery

#### Implementation Details
- Create report templates with Filament reports
- Implement report generation service using Filament actions
- Design filter interface for report parameters with Filament filters
- Set up PDF and Excel export using Filament exports
- Implement scheduled report generation with Filament scheduler integration

### 3.8 Farmer Dashboard Module

#### Components
- Farmer profile overview
- Seedling distribution summary
- Growth tracking timeline
- Interactive location map
- Performance metrics
- Weather and environmental data
- Upcoming monitoring schedule
- Historical data visualization

#### Implementation Details
- Create a responsive dashboard layout using Filament panels and pages
- Implement a tabbed interface using Filament tabs component
- Integrate Leaflet.js for individual farmer location mapping as a Filament widget
- Create dynamic charts for growth progress using Filament chart widgets
- Design a timeline component for monitoring history as a custom Filament widget
- Set up real-time weather data integration using Filament API resources
- Implement a photo gallery for seedling growth documentation with Filament media library
- Create printable farmer reports using Filament actions and exports

#### Dashboard Sections

1. **Profile Header**
   - Farmer photo and contact information
   - Location details with mini-map
   - Quick stats (total seedlings, success rate, time in program)
   - Action buttons (add distribution, schedule monitoring, contact)

2. **Seedling Inventory**
   - List of all distributed seedlings with details
   - Status indicators for each seedling batch
   - Filtering by seedling type, distribution date, and status
   - Growth stage visualization

3. **Location & Environment**
   - Interactive map showing farmer's location and all seedling planting sites
   - Environmental conditions history
   - Soil quality data (if available)
   - Weather forecast for the area
   - Terrain and elevation data

4. **Growth Monitoring**
   - Timeline of all monitoring activities
   - Photo gallery showing seedling progress over time
   - Growth metrics compared to expected timelines
   - Notes and observations from field visits
   - Upcoming scheduled monitoring visits

5. **Performance Analytics**
   - Success rate compared to regional averages
   - Growth rate charts
   - Environmental impact metrics
   - Sustainability indicators

6. **Support & Resources**
   - Training materials specific to farmer's seedlings
   - Communication history
   - Support requests and status
   - Educational resources

## 4. System Improvements

### 4.1 Technical Improvements

1. **Progressive Web App Implementation**
   - Enable offline functionality for field workers
   - Add push notifications for important events
   - Implement background sync for data collection in areas with poor connectivity

2. **AI-Powered Analytics**
   - Implement machine learning for growth prediction
   - Add image recognition for automated seedling health assessment
   - Create predictive models for optimal distribution planning

3. **Enhanced Mobile Experience**
   - Optimize all interfaces for mobile-first usage
   - Add mobile-specific features like camera integration and GPS tracking
   - Implement gesture-based navigation for field use

4. **Integration Capabilities**
   - Create APIs for integration with other agricultural systems
   - Implement data exchange with government agricultural databases
   - Add support for IoT devices like soil sensors and weather stations

### 4.2 User Experience Improvements

1. **Personalized Dashboards**
   - Allow users to customize their dashboard layouts
   - Implement role-specific default views
   - Create saved views for common tasks

2. **Streamlined Workflows**
   - Reduce clicks required for common tasks
   - Implement batch operations for efficiency
   - Add contextual help and tooltips

3. **Enhanced Communication**
   - Add in-app messaging between supervisors and farmers
   - Implement automated SMS notifications for critical events
   - Create a community forum for knowledge sharing

4. **Accessibility Enhancements**
   - Ensure WCAG 2.1 compliance
   - Add support for screen readers
   - Implement high-contrast mode and text scaling

### 4.3 Business Value Improvements

1. **Impact Measurement**
   - Create comprehensive analytics for program effectiveness
   - Implement carbon sequestration tracking
   - Add economic impact assessment tools

2. **Donor Engagement**
   - Create donor-specific views showing impact of contributions
   - Implement "adopt a farmer" functionality
   - Add storytelling components with farmer success stories

3. **Scalability Enhancements**
   - Design system architecture to support growth to 100,000+ farmers
   - Implement data partitioning for performance
   - Create multi-region support for international expansion

4. **Sustainability Features**
   - Add environmental impact dashboards
   - Implement SDG (Sustainable Development Goals) tracking
   - Create biodiversity impact assessment tools

## 5. Implementation Plan

### 5.1 Project Setup and Infrastructure (Week 1)

#### Tasks
1. Set up Laravel project with Filament admin panel
   - Install Laravel 10.x
   - Install Filament v3.x and required plugins
   - Configure database connections
   - Set up development environment

2. Configure version control and deployment
   - Initialize Git repository
   - Set up GitHub Actions for CI/CD
   - Configure staging and production environments

3. Set up project structure
   - Organize directory structure
   - Create initial README and documentation
   - Define coding standards and conventions

#### Deliverables
- Working development environment with Filament installed
- Git repository with initial commit
- CI/CD pipeline configuration
- Project documentation structure

### 5.2 Core Data Models and Authentication (Weeks 2-3)

#### Tasks
1. Implement database migrations for core entities
   - Users, Farmers, Communities tables
   - Seedlings, Inventory tables
   - Distributions, Monitoring tables
   - Create necessary relationships and foreign keys

2. Set up authentication and authorization
   - Configure Filament authentication
   - Install and configure Filament Shield for RBAC
   - Create user roles and permissions
   - Implement login, registration, and password reset

3. Create base Filament resources
   - Generate resource classes for all core entities
   - Configure basic list, create, edit, and delete operations
   - Set up form validation rules

#### Deliverables
- Complete database schema with migrations
- Working authentication system with role-based access
- Basic CRUD operations for all core entities
- User management interface

### 5.3 Procurement and Inventory Module (Weeks 4-5)

#### Tasks
1. Implement supplier management
   - Create supplier resource with CRUD operations
   - Implement supplier evaluation and rating system
   - Set up supplier contact management

2. Build inventory tracking system
   - Implement inventory resource with stock management
   - Create batch tracking functionality
   - Set up inventory status calculations
   - Implement low-stock alerts and notifications

3. Develop procurement workflow
   - Create procurement request process
   - Implement approval workflows with state transitions
   - Set up procurement reporting

#### Deliverables
- Complete supplier management system
- Functional inventory tracking with batch management
- Procurement workflow with approvals
- Low-stock notification system

### 5.4 Distribution Management Module (Weeks 6-7)

#### Tasks
1. Implement farmer and community registration
   - Create registration forms with validation
   - Implement location capture with maps integration
   - Set up farmer/community profiles

2. Build seedling distribution system
   - Create distribution allocation interface
   - Implement QR code generation for distributions
   - Set up distribution status tracking

3. Develop distribution reporting
   - Create distribution reports with filters
   - Implement export functionality
   - Set up distribution analytics

#### Deliverables
- Farmer and community registration system
- Seedling distribution management interface
- QR code generation and scanning capability
- Distribution reporting and analytics

### 5.5 Monitoring and Growth Tracking Module (Weeks 8-9)

#### Tasks
1. Implement growth stage framework
   - Define growth stage models for different seedling types
   - Create growth stage visualization
   - Set up expected vs. actual growth comparison

2. Build monitoring data collection
   - Create monitoring forms with image upload
   - Implement GPS location capture
   - Set up environmental data collection

3. Develop offline capabilities
   - Implement PWA functionality
   - Set up offline data storage and sync
   - Create mobile-optimized monitoring interface

#### Deliverables
- Growth stage tracking system
- Monitoring data collection forms
- Image upload and gallery functionality
- Offline-capable monitoring application

### 5.6 Map Visualization Module (Weeks 10-11)

#### Tasks
1. Implement map integration
   - Set up Leaflet.js within Filament
   - Create map widget for dashboards
   - Implement marker clustering

2. Build location-based features
   - Create farmer/distribution location mapping
   - Implement filtering capabilities
   - Set up information popups

3. Develop timeline visualization
   - Create timeline slider component
   - Implement historical view of distributions
   - Set up time-based filtering

#### Deliverables
- Interactive map visualization
- Location-based filtering and search
- Timeline visualization component
- Public and private map views

### 5.7 Dashboard and Reporting (Weeks 12-13)

#### Tasks
1. Implement role-specific dashboards
   - Create admin, supervisor, and farmer dashboards
   - Implement dashboard widgets
   - Set up real-time data updates

2. Build farmer-specific dashboard
   - Create farmer profile overview
   - Implement seedling inventory view
   - Set up growth monitoring timeline
   - Create performance analytics

3. Develop reporting system
   - Create standard report templates
   - Implement custom report builder
   - Set up scheduled report generation
   - Create export functionality

#### Deliverables
- Role-specific dashboards
- Comprehensive farmer dashboard
- Standard and custom reporting system
- Scheduled report generation

### 5.8 Testing and Optimization (Week 14)

#### Tasks
1. Perform comprehensive testing
   - Conduct unit and feature tests
   - Perform user acceptance testing
   - Test mobile and offline functionality

2. Optimize performance
   - Implement database query optimization
   - Set up caching where appropriate
   - Optimize asset loading

3. Conduct security audit
   - Review authentication and authorization
   - Test for common vulnerabilities
   - Implement security best practices

#### Deliverables
- Test coverage report
- Performance optimization report
- Security audit results and fixes

### 5.9 Deployment and Training (Week 15)

#### Tasks
1. Prepare production environment
   - Configure production server
   - Set up SSL certificates
   - Configure backups and monitoring

2. Deploy application
   - Perform final testing in staging
   - Deploy to production
   - Conduct post-deployment verification

3. Conduct user training
   - Create user documentation
   - Conduct training sessions
   - Set up support channels

#### Deliverables
- Production-ready application
- User documentation and training materials
- Support system setup

### 5.10 Post-Launch Support and Iteration (Ongoing)

#### Tasks
1. Provide post-launch support
   - Monitor application performance
   - Address user feedback and issues
   - Implement bug fixes

2. Plan feature iterations
   - Collect user feedback
   - Prioritize feature enhancements
   - Plan development sprints

3. Implement continuous improvement
   - Regular code reviews
   - Performance optimization
   - Security updates

#### Deliverables
- Regular status reports
- Feature enhancement roadmap
- Ongoing maintenance plan
